<?php

namespace Jeffersongoncalves\Shopify;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ShopifyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-shopify')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations();
    }
}
