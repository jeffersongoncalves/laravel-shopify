<?php

namespace JeffersonGoncalves\Shopify\Resources;

use JeffersonGoncalves\Shopify\ShopifyClient;

/**
 * Customer endpoints. `search()` takes Shopify's query syntax, e.g.
 * `email:ada@example.com` or `country:BR AND state:enabled`.
 */
class Customers
{
    public function __construct(private readonly ShopifyClient $client) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function list(int $limit = 50, array $filters = []): array
    {
        return $this->client->get('/customers', ['limit' => $limit] + $filters);
    }

    /**
     * @return array<string, mixed>
     */
    public function get(int|string $customerId): array
    {
        return $this->client->get('/customers/'.$customerId);
    }

    /**
     * @return array<string, mixed>
     */
    public function search(string $query, int $limit = 50): array
    {
        return $this->client->get('/customers/search', ['query' => $query, 'limit' => $limit]);
    }

    /**
     * @return array<string, mixed>
     */
    public function count(): array
    {
        return $this->client->get('/customers/count');
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function create(array $attributes): array
    {
        return $this->client->post('/customers', ['customer' => $attributes]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function update(int|string $customerId, array $attributes): array
    {
        return $this->client->put('/customers/'.$customerId, [
            'customer' => ['id' => $customerId] + $attributes,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function delete(int|string $customerId): array
    {
        return $this->client->delete('/customers/'.$customerId);
    }

    /**
     * @return array<string, mixed>
     */
    public function orders(int|string $customerId): array
    {
        return $this->client->get('/customers/'.$customerId.'/orders');
    }
}
