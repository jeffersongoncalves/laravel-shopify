<?php

namespace JeffersonGoncalves\Shopify\Resources;

use JeffersonGoncalves\Shopify\ShopifyClient;

/**
 * Webhook subscriptions — `orders/create`, `orders/paid`, `products/update` and
 * the rest of the topic list. Shopify only delivers to HTTPS endpoints.
 */
class Webhooks
{
    public function __construct(private readonly ShopifyClient $client) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function list(array $filters = []): array
    {
        return $this->client->get('/webhooks', $filters);
    }

    /**
     * @return array<string, mixed>
     */
    public function get(int|string $webhookId): array
    {
        return $this->client->get('/webhooks/'.$webhookId);
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function create(string $topic, string $address, array $attributes = []): array
    {
        return $this->client->post('/webhooks', [
            'webhook' => ['topic' => $topic, 'address' => $address, 'format' => 'json'] + $attributes,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function delete(int|string $webhookId): array
    {
        return $this->client->delete('/webhooks/'.$webhookId);
    }
}
