<?php

namespace JeffersonGoncalves\Shopify\Tests;

use JeffersonGoncalves\Shopify\ShopifyServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ShopifyServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('shopify.shop_domain', 'fake-store.myshopify.com');
        $app['config']->set('shopify.access_token', 'fake-access-token');
        $app['config']->set('shopify.api_version', '2024-01');
        $app['config']->set('shopify.base_url', null);
    }
}
