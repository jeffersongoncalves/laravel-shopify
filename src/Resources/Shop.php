<?php

namespace JeffersonGoncalves\Shopify\Resources;

use JeffersonGoncalves\Shopify\ShopifyClient;

/**
 * The store itself — name, plan, currency, timezone and the rest of the shop
 * record. Also the cheapest call to verify a token works.
 */
class Shop
{
    public function __construct(private readonly ShopifyClient $client) {}

    /**
     * @return array<string, mixed>
     */
    public function get(): array
    {
        return $this->client->get('/shop');
    }
}
