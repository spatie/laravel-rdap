<?php

namespace Spatie\Rdap;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class RdapServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-rdap')
            ->hasConfigFile();

        $this->app->bind('rdap', Rdap::class);
    }
}
