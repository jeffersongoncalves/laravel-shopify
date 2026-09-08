<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Shopify\Facades\Shopify;

beforeEach(fn () => fakeShopify(['fake-store.myshopify.com/admin/api/2024-01/*' => Http::response(['webhook' => ['id' => 1]], 200)]));

it('subscribes to a topic with a json format by default', function () {
    Shopify::webhooks()->create('orders/create', 'https://example.com/hooks/orders');

    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && $request['webhook'] === [
            'topic' => 'orders/create',
            'address' => 'https://example.com/hooks/orders',
            'format' => 'json',
        ]);
});

it('deletes a subscription', function () {
    Shopify::webhooks()->delete(1);

    Http::assertSent(fn (Request $request) => $request->method() === 'DELETE'
        && str_contains($request->url(), '/webhooks/1.json'));
});
