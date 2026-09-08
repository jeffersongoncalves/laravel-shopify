<div class="filament-hidden">

![Laravel Shopify](https://raw.githubusercontent.com/jeffersongoncalves/laravel-shopify/main/art/jeffersongoncalves-laravel-shopify.png)

</div>

# Laravel Shopify

[![Tests](https://github.com/jeffersongoncalves/laravel-shopify/actions/workflows/tests.yml/badge.svg)](https://github.com/jeffersongoncalves/laravel-shopify/actions/workflows/tests.yml)
[![PHPStan](https://github.com/jeffersongoncalves/laravel-shopify/actions/workflows/phpstan.yml/badge.svg)](https://github.com/jeffersongoncalves/laravel-shopify/actions/workflows/phpstan.yml)
[![Code Style](https://github.com/jeffersongoncalves/laravel-shopify/actions/workflows/pint.yml/badge.svg)](https://github.com/jeffersongoncalves/laravel-shopify/actions/workflows/pint.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/jeffersongoncalves/laravel-shopify.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-shopify)
[![Total Downloads](https://img.shields.io/packagist/dt/jeffersongoncalves/laravel-shopify.svg?style=flat-square)](https://packagist.org/packages/jeffersongoncalves/laravel-shopify)
[![License](https://img.shields.io/packagist/l/jeffersongoncalves/laravel-shopify.svg?style=flat-square)](LICENSE.md)

A Laravel client for the [Shopify](https://shopify.com) Admin API. A fluent `Shopify` facade groups the shop, product, order, customer, and webhook endpoints behind resource accessors, signs every call with the `X-Shopify-Access-Token` header, and throws a `ShopifyException` on a non-2xx response instead of returning a silent error array. The GraphQL Admin API is one call away for everything REST cannot express.

## Features

- **Shop** — `shop()->get()`
- **Products** — `products()->list()`, `get()`, `count()`, `create()`, `update()`, `delete()`, `variants()`
- **Orders** — `orders()->list()`, `get()`, `count()`, `close()`, `open()`, `cancel()`, `fulfillments()`
- **Customers** — `customers()->list()`, `get()`, `search()`, `count()`, `create()`, `update()`, `delete()`, `orders()`
- **Webhooks** — `webhooks()->list()`, `get()`, `create()`, `delete()`
- **GraphQL** — `Shopify::graphql($query, $variables)` returns the `data` payload and turns an `errors` array into an exception, even though Shopify answers those with HTTP 200
- **Escape hatch** — `Shopify::get()`, `post()`, `put()`, `delete()` reach any endpoint the resources do not cover yet
- **Forgiving shop domain** — `my-store`, `my-store.myshopify.com` and `https://my-store.myshopify.com/admin` all work
- **Thin by design** — every method returns the raw decoded JSON response as an array, no DTOs
- **Fails loud** — a non-2xx response throws `ShopifyException` carrying the API's error message and HTTP status code

## Installation

```bash
composer require jeffersongoncalves/laravel-shopify
```

Optionally publish the config file:

```bash
php artisan vendor:publish --tag="shopify-config"
```

## Configuration

Add to your `.env`:

```env
SHOPIFY_SHOP_DOMAIN=my-store.myshopify.com
SHOPIFY_ACCESS_TOKEN=shpat_xxxxxxxxxxxxxxxxxxxxxxxx
SHOPIFY_API_VERSION=2024-01
```

Create the access token as a custom app in your store under **Settings → Apps and sales channels → Develop apps**, then grant it the Admin API scopes you need (`read_products`, `write_orders`, ...). A token from a public app's OAuth flow works the same way.

### Config Options

```php
// config/shopify.php
return [
    'shop_domain' => env('SHOPIFY_SHOP_DOMAIN'),
    'access_token' => env('SHOPIFY_ACCESS_TOKEN'),
    'api_version' => env('SHOPIFY_API_VERSION', '2024-01'),
    'base_url' => env('SHOPIFY_BASE_URL'),
    'timeout' => (int) env('SHOPIFY_TIMEOUT', 30),
];
```

`base_url` is built from the shop domain and API version when left unset — set it only to point the client at something else, such as a local mock server.

## Usage

```php
use JeffersonGoncalves\Shopify\Facades\Shopify;
use JeffersonGoncalves\Shopify\Exceptions\ShopifyException;
```

### Shop

```php
Shopify::shop()->get();
```

### Products

```php
Shopify::products()->list(limit: 50, filters: ['vendor' => 'Acme', 'status' => 'active']);
Shopify::products()->get(123);
Shopify::products()->count();
Shopify::products()->variants(123);

Shopify::products()->create([
    'title' => 'Product Name',
    'body_html' => '<p>Description</p>',
    'vendor' => 'Brand',
    'product_type' => 'Category',
    'variants' => [
        ['price' => '99.00', 'sku' => 'SKU-001'],
    ],
]);

Shopify::products()->update(123, ['title' => 'Renamed']);
Shopify::products()->delete(123);
```

### Orders

```php
// Shopify defaults to open orders only; this defaults to `any`
Shopify::orders()->list(limit: 50, status: 'any');
Shopify::orders()->get(42);
Shopify::orders()->fulfillments(42);
Shopify::orders()->cancel(42, ['reason' => 'customer']);
Shopify::orders()->close(42);
Shopify::orders()->open(42);
```

### Customers

```php
Shopify::customers()->list(limit: 50);
Shopify::customers()->get(7);
Shopify::customers()->search('email:ada@example.com');
Shopify::customers()->orders(7);

Shopify::customers()->create(['email' => 'ada@example.com', 'first_name' => 'Ada']);
Shopify::customers()->update(7, ['last_name' => 'Lovelace']);
Shopify::customers()->delete(7);
```

### Webhooks

```php
Shopify::webhooks()->create('orders/create', 'https://example.com/hooks/orders');
Shopify::webhooks()->list();
Shopify::webhooks()->delete(1);
```

Common topics: `orders/create`, `orders/paid`, `orders/fulfilled`, `customers/create`, `products/update`, `checkouts/create`.

### GraphQL

```php
$data = Shopify::graphql(<<<'GQL'
    query ($first: Int!) {
        products(first: $first) {
            edges {
                node { id title totalInventory }
            }
        }
    }
GQL, ['first' => 10]);

$data['products']['edges'];
```

### Any other endpoint

```php
Shopify::get('/inventory_levels', ['location_ids' => '123']);
Shopify::post('/price_rules', ['price_rule' => [/* ... */]]);
```

### Handling errors

```php
try {
    $products = Shopify::products()->list();
} catch (ShopifyException $e) {
    // $e->getMessage() — the API's error message, or the raw response body
    // $e->statusCode  — the HTTP status returned by Shopify (0 for config errors)
}
```

### Rate limits

The REST Admin API allows about 2 requests per second per store and answers a burst with `429 Too Many Requests`, which surfaces here as a `ShopifyException` with `statusCode` 429. Wrap bulk work in Laravel's HTTP retry or a queued job with backoff when you push that hard.

## Testing

```bash
composer test
```

## Static Analysis

```bash
composer analyse
```

## Code Formatting

```bash
composer format
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security

Please review [our security policy](.github/SECURITY.md) on how to report security vulnerabilities.

## Credits

- [Jefferson Gonçalves](https://github.com/jeffersongoncalves)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
