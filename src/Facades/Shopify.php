<?php

namespace Jeffersongoncalves\Shopify\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Jeffersongoncalves\Shopify\Shopify
 */
class Shopify extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-shopify';
    }
}
