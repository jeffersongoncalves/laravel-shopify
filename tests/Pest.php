<?php

use Illuminate\Support\Facades\Http;
use JeffersonGoncalves\Shopify\Tests\TestCase;

uses(TestCase::class)
    ->beforeEach(fn () => Http::preventStrayRequests())
    ->in('Feature');

/**
 * @param  array<string, mixed>  $fakes
 */
function fakeShopify(array $fakes): void
{
    Http::fake($fakes);
}
