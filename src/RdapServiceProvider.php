<?php

namespace Spatie\Rdap;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Rdap\Commands\RdapCommand;

class RdapServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-rdap')
            ->hasConfigFile();
    }
}
