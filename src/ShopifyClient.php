<?php

namespace JeffersonGoncalves\Shopify;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Shopify\Exceptions\ShopifyException;
use JeffersonGoncalves\Shopify\Resources\Customers;
use JeffersonGoncalves\Shopify\Resources\Orders;
use JeffersonGoncalves\Shopify\Resources\Products;
use JeffersonGoncalves\Shopify\Resources\Shop;
use JeffersonGoncalves\Shopify\Resources\Webhooks;

/**
 * Thin fluent client for the Shopify Admin API. Groups endpoints behind resource
 * accessors, signs every call with the `X-Shopify-Access-Token` header, and
 * exposes the GraphQL endpoint for the queries REST cannot express.
 */
class ShopifyClient
{
    public function shop(): Shop
    {
        return new Shop($this);
    }

    public function products(): Products
    {
        return new Products($this);
    }

    public function orders(): Orders
    {
        return new Orders($this);
    }

    public function customers(): Customers
    {
        return new Customers($this);
    }

    public function webhooks(): Webhooks
    {
        return new Webhooks($this);
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<string, mixed>
     *
     * @throws ShopifyException
     */
    public function get(string $uri, array $query = []): array
    {
        return $this->handle($this->http()->get($this->path($uri), $query));
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
     * @throws ShopifyException
     */
    public function post(string $uri, array $body = []): array
    {
        return $this->handle($this->http()->post($this->path($uri), $body));
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     *
     * @throws ShopifyException
     */
    public function put(string $uri, array $body = []): array
    {
        return $this->handle($this->http()->put($this->path($uri), $body));
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ShopifyException
     */
    public function delete(string $uri): array
    {
        return $this->handle($this->http()->delete($this->path($uri)));
    }

    /**
     * Runs a GraphQL Admin API query. Shopify answers a failed GraphQL call with
     * HTTP 200 and an `errors` array, so that case is turned into an exception
     * here rather than handed back as a silent success.
     *
     * @param  array<string, mixed>  $variables
     * @return array<string, mixed> the `data` payload
     *
     * @throws ShopifyException
     */
    public function graphql(string $query, array $variables = []): array
    {
        $payload = ['query' => $query];

        if ($variables !== []) {
            $payload['variables'] = $variables;
        }

        $response = $this->handle($this->http()->post($this->path('/graphql.json'), $payload));

        if (isset($response['errors']) && $response['errors'] !== []) {
            throw new ShopifyException($this->graphqlErrorMessage($response['errors']), 200);
        }

        $data = $response['data'] ?? [];

        return is_array($data) ? $data : [];
    }

    /**
     * The API root for the configured shop and version, e.g.
     * `https://my-store.myshopify.com/admin/api/2024-01`.
     *
     * @throws ShopifyException
     */
    public function baseUrl(): string
    {
        $baseUrl = config('shopify.base_url');

        if (is_string($baseUrl) && $baseUrl !== '') {
            return rtrim($baseUrl, '/');
        }

        $version = (string) config('shopify.api_version', '2024-01');

        return 'https://'.$this->shopDomain().'/admin/api/'.$version;
    }

    /**
     * Normalises whatever is configured — `my-store`, `my-store.myshopify.com`
     * or `https://my-store.myshopify.com/admin` — to the bare myshopify host.
     *
     * @throws ShopifyException
     */
    public function shopDomain(): string
    {
        $domain = config('shopify.shop_domain');

        if (! is_string($domain) || trim($domain) === '') {
            throw new ShopifyException('shopify.shop_domain is not configured.');
        }

        $domain = trim($domain);
        $domain = (string) preg_replace('#^https?://#i', '', $domain);
        $domain = strtok($domain, '/') ?: $domain;

        return str_contains($domain, '.') ? $domain : $domain.'.myshopify.com';
    }

    /**
     * @throws ShopifyException
     */
    private function http(): PendingRequest
    {
        $token = config('shopify.access_token');

        if (! is_string($token) || $token === '') {
            throw new ShopifyException('shopify.access_token is not configured.');
        }

        return Http::baseUrl($this->baseUrl())
            ->timeout((int) config('shopify.timeout', 30))
            ->acceptJson()
            ->withHeaders(['X-Shopify-Access-Token' => $token]);
    }

    /**
     * Every REST endpoint is a `.json` resource; appending it here keeps the
     * suffix out of every resource method.
     */
    private function path(string $uri): string
    {
        $uri = '/'.ltrim($uri, '/');

        return str_contains($uri, '.json') ? $uri : $uri.'.json';
    }

    /**
     * @return array<string, mixed>
     *
     * @throws ShopifyException
     */
    private function handle(Response $response): array
    {
        if ($response->failed()) {
            throw new ShopifyException($this->errorMessage($response), $response->status());
        }

        $data = $response->json();

        return is_array($data) ? $data : [];
    }

    private function errorMessage(Response $response): string
    {
        $data = $response->json();
        $errors = is_array($data) ? ($data['errors'] ?? $data['error'] ?? null) : null;

        if (is_string($errors) && $errors !== '') {
            return $errors;
        }

        // Field validation failures come back as {"errors": {"title": ["can't be blank"]}}.
        if (is_array($errors)) {
            $flat = [];

            foreach ($errors as $field => $messages) {
                foreach ((array) $messages as $message) {
                    $flat[] = is_string($field) ? $field.': '.$message : (string) $message;
                }
            }

            if ($flat !== []) {
                return implode(', ', $flat);
            }
        }

        return $response->body() !== ''
            ? $response->body()
            : "Shopify API request failed with status {$response->status()}.";
    }

    /**
     * @param  array<int, mixed>  $errors
     */
    private function graphqlErrorMessage(array $errors): string
    {
        $messages = [];

        foreach ($errors as $error) {
            $message = is_array($error) ? ($error['message'] ?? null) : $error;

            if (is_string($message) && $message !== '') {
                $messages[] = $message;
            }
        }

        return $messages !== [] ? implode(', ', $messages) : 'The Shopify GraphQL request returned errors.';
    }
}
