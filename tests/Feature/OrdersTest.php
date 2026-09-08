<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Shopify\Facades\Shopify;

beforeEach(fn () => fakeShopify(['fake-store.myshopify.com/admin/api/2024-01/*' => Http::response(['orders' => []], 200)]));

it('asks for orders of any status by default', function () {
    Shopify::orders()->list();

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'status=any')
        && str_contains($request->url(), 'limit=50'));
});

it('lets the caller narrow the status', function () {
    Shopify::orders()->list(status: 'closed');

    Http::assertSent(fn (Request $request) => str_contains($request->url(), 'status=closed'));
});

it('cancels an order', function () {
    Shopify::orders()->cancel(42, ['reason' => 'customer']);

    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && str_contains($request->url(), '/orders/42/cancel.json')
        && $request['reason'] === 'customer');
});

it('reads the fulfillments of an order', function () {
    Shopify::orders()->fulfillments(42);

    Http::assertSent(fn (Request $request) => str_contains($request->url(), '/orders/42/fulfillments.json'));
});
