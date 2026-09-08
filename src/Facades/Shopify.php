<?php

namespace JeffersonGoncalves\Shopify\Facades;

use Illuminate\Support\Facades\Facade;
use JeffersonGoncalves\Shopify\Resources\Customers;
use JeffersonGoncalves\Shopify\Resources\Orders;
use JeffersonGoncalves\Shopify\Resources\Products;
use JeffersonGoncalves\Shopify\Resources\Shop;
use JeffersonGoncalves\Shopify\Resources\Webhooks;
use JeffersonGoncalves\Shopify\ShopifyClient;

/**
 * @method static Shop shop()
 * @method static Products products()
 * @method static Orders orders()
 * @method static Customers customers()
 * @method static Webhooks webhooks()
 * @method static array<string, mixed> get(string $uri, array<string, mixed> $query = [])
 * @method static array<string, mixed> post(string $uri, array<string, mixed> $body = [])
 * @method static array<string, mixed> put(string $uri, array<string, mixed> $body = [])
 * @method static array<string, mixed> delete(string $uri)
 * @method static array<string, mixed> graphql(string $query, array<string, mixed> $variables = [])
 * @method static string baseUrl()
 * @method static string shopDomain()
 *
 * @see ShopifyClient
 */
class Shopify extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'shopify';
    }
}
