<?php

namespace JCSoriano\LaravelCrudTemplates;

use JCSoriano\LaravelCrudTemplates\Commands\GenerateCrud;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelCrudTemplatesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-crud-templates')
            ->hasCommand(GenerateCrud::class);
    }

    public function packageBooted(): void
    {
        // Publish stubs
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/stubs/api' => base_path('stubs'),
            ], 'laravel-crud-templates-stubs');
        }
    }

    public function register(): void
    {
        parent::register();

        $this->app->singleton(LaravelCrudTemplates::class, function () {
            return new LaravelCrudTemplates;
        });

        $this->app->singleton(LaravelStub::class, function () {
            return new LaravelStub;
        });
    }
}
