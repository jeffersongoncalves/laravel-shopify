<?php

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Shopify\Exceptions\ShopifyException;
use JeffersonGoncalves\Shopify\Facades\Shopify;

it('builds the base url from the shop domain and api version', function () {
    expect(Shopify::baseUrl())->toBe('https://fake-store.myshopify.com/admin/api/2024-01');
});

it('normalises a bare handle, a full domain and an admin url to the same host', function (string $configured) {
    config()->set('shopify.shop_domain', $configured);

    expect(Shopify::shopDomain())->toBe('fake-store.myshopify.com');
})->with([
    'fake-store',
    'fake-store.myshopify.com',
    'https://fake-store.myshopify.com/admin',
]);

it('honours an explicit base url', function () {
    config()->set('shopify.base_url', 'http://localhost:8080/admin/api/2024-01/');

    expect(Shopify::baseUrl())->toBe('http://localhost:8080/admin/api/2024-01');
});

it('signs requests with the access token header', function () {
    fakeShopify(['fake-store.myshopify.com/admin/api/2024-01/shop.json' => Http::response(['shop' => ['id' => 1]], 200)]);

    Shopify::shop()->get();

    Http::assertSent(fn (Request $request) => $request->header('X-Shopify-Access-Token') === ['fake-access-token']);
});

it('throws when the shop domain is missing', function () {
    config()->set('shopify.shop_domain', null);

    Shopify::shop()->get();
})->throws(ShopifyException::class, 'shopify.shop_domain is not configured.');

it('throws when the access token is missing', function () {
    config()->set('shopify.access_token', '');

    Shopify::shop()->get();
})->throws(ShopifyException::class, 'shopify.access_token is not configured.');

it('throws with the api error message and status on a failed response', function () {
    fakeShopify(['*' => Http::response(['errors' => 'Not Found'], 404)]);

    try {
        Shopify::products()->get(999);
        expect()->fail('no exception thrown');
    } catch (ShopifyException $e) {
        expect($e->getMessage())->toBe('Not Found')
            ->and($e->statusCode)->toBe(404);
    }
});

it('flattens field validation errors into one message', function () {
    fakeShopify(['*' => Http::response(['errors' => ['title' => ["can't be blank"]]], 422)]);

    expect(fn () => Shopify::products()->create([]))
        ->toThrow(ShopifyException::class, "title: can't be blank");
});

it('returns the data payload of a graphql query', function () {
    fakeShopify([
        'fake-store.myshopify.com/admin/api/2024-01/graphql.json' => Http::response([
            'data' => ['shop' => ['name' => 'Fake Store']],
        ], 200),
    ]);

    expect(Shopify::graphql('{ shop { name } }'))->toBe(['shop' => ['name' => 'Fake Store']]);
});

it('sends graphql variables only when given', function () {
    fakeShopify(['*' => Http::response(['data' => []], 200)]);

    Shopify::graphql('query($n: Int!) { products(first: $n) { edges { node { id } } } }', ['n' => 5]);

    Http::assertSent(fn (Request $request) => $request['variables'] === ['n' => 5]);
});

it('throws on a graphql response carrying errors despite the 200 status', function () {
    fakeShopify(['*' => Http::response(['errors' => [['message' => 'Field does not exist']]], 200)]);

    expect(fn () => Shopify::graphql('{ nope }'))
        ->toThrow(ShopifyException::class, 'Field does not exist');
});
