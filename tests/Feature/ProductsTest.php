<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Shopify\Facades\Shopify;

beforeEach(fn () => fakeShopify(['fake-store.myshopify.com/admin/api/2024-01/*' => Http::response(['product' => ['id' => 1]], 200)]));

it('lists products with a limit and extra filters', function () {
    Shopify::products()->list(10, ['vendor' => 'Acme']);

    Http::assertSent(fn (Request $request) => $request->method() === 'GET'
        && str_contains($request->url(), '/products.json')
        && str_contains($request->url(), 'limit=10')
        && str_contains($request->url(), 'vendor=Acme'));
});

it('gets a single product', function () {
    expect(Shopify::products()->get(123))->toBe(['product' => ['id' => 1]]);

    Http::assertSent(fn (Request $request) => str_contains($request->url(), '/products/123.json'));
});

it('wraps the attributes of a created product', function () {
    Shopify::products()->create(['title' => 'Product Name', 'vendor' => 'Brand']);

    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && $request['product'] === ['title' => 'Product Name', 'vendor' => 'Brand']);
});

it('puts the id inside the payload on update', function () {
    Shopify::products()->update(123, ['title' => 'Renamed']);

    Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
        && $request['product'] === ['id' => 123, 'title' => 'Renamed']);
});

it('deletes a product', function () {
    Shopify::products()->delete(123);

    Http::assertSent(fn (Request $request) => $request->method() === 'DELETE'
        && str_contains($request->url(), '/products/123.json'));
});
