<?php

namespace JeffersonGoncalves\Shopify;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ShopifyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('shopify')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(ShopifyClient::class);
        $this->app->alias(ShopifyClient::class, 'shopify');
    }
}
