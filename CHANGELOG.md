# Changelog

All notable changes to this project will be documented in this file.

## 1.0.0 - 2026-09-08

First release.

- `shop()` — store record, the cheapest way to verify a token
- `products()` — list, get, count, create, update, delete, variants
- `orders()` — list (any status by default), get, count, close, open, cancel, fulfillments
- `customers()` — list, get, search, count, create, update, delete, orders
- `webhooks()` — list, get, create, delete
- `Shopify::graphql()` for the Admin GraphQL API, with the HTTP 200 `errors` payload turned into an exception
- `Shopify::get()/post()/put()/delete()` as an escape hatch for uncovered endpoints
- Shop domain accepts a handle, a myshopify domain or an admin URL
- `ShopifyException` on any non-2xx response, carrying the API message and status code

## [Unreleased]
