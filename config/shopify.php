<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Shop Domain
    |--------------------------------------------------------------------------
    |
    | The store this package talks to. Give it the handle (`my-store`), the full
    | myshopify domain (`my-store.myshopify.com`) or the admin URL — all three
    | are normalised to the same base URL.
    |
    */
    'shop_domain' => env('SHOPIFY_SHOP_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Admin API Access Token
    |--------------------------------------------------------------------------
    |
    | Sent as the `X-Shopify-Access-Token` header on every request. Create it as
    | a custom app under Settings > Apps and sales channels > Develop apps, or
    | take it from the OAuth flow of a public app.
    |
    */
    'access_token' => env('SHOPIFY_ACCESS_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | API Version
    |--------------------------------------------------------------------------
    |
    | Shopify versions its Admin API quarterly (`2024-01`, `2024-04`, ...) and
    | supports each one for a year. Pin the version you tested against and bump
    | it deliberately.
    |
    */
    'api_version' => env('SHOPIFY_API_VERSION', '2024-01'),

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | Built from the shop domain and API version when left null. Set it only if
    | you need to point the client somewhere else, such as a local mock server.
    |
    */
    'base_url' => env('SHOPIFY_BASE_URL'),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | Seconds to wait for a response before giving up.
    |
    */
    'timeout' => (int) env('SHOPIFY_TIMEOUT', 30),
];
