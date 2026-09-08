<?php

namespace JeffersonGoncalves\Shopify\Resources;

use JeffersonGoncalves\Shopify\ShopifyClient;

/**
 * Order endpoints. Shopify defaults `status` to `open`, so `list()` asks for
 * `any` — the surprising default is the one people report as a bug.
 */
class Orders
{
    public function __construct(private readonly ShopifyClient $client) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function list(int $limit = 50, string $status = 'any', array $filters = []): array
    {
        return $this->client->get('/orders', ['limit' => $limit, 'status' => $status] + $filters);
    }

    /**
     * @return array<string, mixed>
     */
    public function get(int|string $orderId): array
    {
        return $this->client->get('/orders/'.$orderId);
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function count(array $filters = []): array
    {
        return $this->client->get('/orders/count', ['status' => 'any'] + $filters);
    }

    /**
     * @return array<string, mixed>
     */
    public function close(int|string $orderId): array
    {
        return $this->client->post('/orders/'.$orderId.'/close');
    }

    /**
     * @return array<string, mixed>
     */
    public function open(int|string $orderId): array
    {
        return $this->client->post('/orders/'.$orderId.'/open');
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function cancel(int|string $orderId, array $attributes = []): array
    {
        return $this->client->post('/orders/'.$orderId.'/cancel', $attributes);
    }

    /**
     * @return array<string, mixed>
     */
    public function fulfillments(int|string $orderId): array
    {
        return $this->client->get('/orders/'.$orderId.'/fulfillments');
    }
}
