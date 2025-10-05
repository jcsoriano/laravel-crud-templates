<?php

namespace JCSoriano\LaravelCrudStubs;

use JCSoriano\LaravelCrudStubs\Commands\GenerateCrud;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelCrudStubsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-crud-stubs')
            ->hasConfigFile('crud-stubs')
            ->hasCommand(GenerateCrud::class);
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(LaravelCrudStubs::class, function () {
            return new LaravelCrudStubs;
        });
    }
}
