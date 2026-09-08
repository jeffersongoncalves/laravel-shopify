<?php

namespace JeffersonGoncalves\Shopify\Resources;

use JeffersonGoncalves\Shopify\ShopifyClient;

/**
 * Product catalog endpoints. `$filters` and `$attributes` are passed straight
 * through to Shopify, so any documented query parameter or product field works
 * without waiting for a wrapper method.
 */
class Products
{
    public function __construct(private readonly ShopifyClient $client) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function list(int $limit = 50, array $filters = []): array
    {
        return $this->client->get('/products', ['limit' => $limit] + $filters);
    }

    /**
     * @return array<string, mixed>
     */
    public function get(int|string $productId): array
    {
        return $this->client->get('/products/'.$productId);
    }

    /**
     * @return array<string, mixed>
     */
    public function count(): array
    {
        return $this->client->get('/products/count');
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function create(array $attributes): array
    {
        return $this->client->post('/products', ['product' => $attributes]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function update(int|string $productId, array $attributes): array
    {
        return $this->client->put('/products/'.$productId, [
            'product' => ['id' => $productId] + $attributes,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function delete(int|string $productId): array
    {
        return $this->client->delete('/products/'.$productId);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function variants(int|string $productId, array $filters = []): array
    {
        return $this->client->get('/products/'.$productId.'/variants', $filters);
    }
}
