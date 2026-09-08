<?php

namespace Jeffersongoncalves\Shopify\Tests;

use Jeffersongoncalves\Shopify\ShopifyServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ShopifyServiceProvider::class,
        ];
    }
}
