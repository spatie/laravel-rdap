<?php

namespace Spatie\Rdap;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\Rdap\Commands\RdapCommand;

class RdapServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-rdap')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-rdap_table')
            ->hasCommand(RdapCommand::class);
    }
}
