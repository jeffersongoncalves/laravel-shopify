<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Shopify\Facades\Shopify;

beforeEach(fn () => fakeShopify(['fake-store.myshopify.com/admin/api/2024-01/*' => Http::response(['customers' => []], 200)]));

it('searches customers with shopify query syntax', function () {
    Shopify::customers()->search('email:ada@example.com');

    Http::assertSent(fn (Request $request) => str_contains($request->url(), '/customers/search.json')
        && $request['query'] === 'email:ada@example.com');
});

it('wraps the attributes of a created customer', function () {
    Shopify::customers()->create(['email' => 'ada@example.com', 'first_name' => 'Ada']);

    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && $request['customer']['email'] === 'ada@example.com');
});

it('reads the orders of a customer', function () {
    Shopify::customers()->orders(7);

    Http::assertSent(fn (Request $request) => str_contains($request->url(), '/customers/7/orders.json'));
});
