<?php

namespace JCSoriano\LaravelCrudTemplates;

use JCSoriano\LaravelCrudTemplates\Commands\GenerateCrud;
use JCSoriano\LaravelCrudTemplates\FieldTypes\BelongsToManyType;
use JCSoriano\LaravelCrudTemplates\FieldTypes\BelongsToType;
use JCSoriano\LaravelCrudTemplates\FieldTypes\BooleanType;
use JCSoriano\LaravelCrudTemplates\FieldTypes\DateTimeType;
use JCSoriano\LaravelCrudTemplates\FieldTypes\DateType;
use JCSoriano\LaravelCrudTemplates\FieldTypes\DecimalType;
use JCSoriano\LaravelCrudTemplates\FieldTypes\EnumType;
use JCSoriano\LaravelCrudTemplates\FieldTypes\HasManyType;
use JCSoriano\LaravelCrudTemplates\FieldTypes\IntegerType;
use JCSoriano\LaravelCrudTemplates\FieldTypes\JsonType;
use JCSoriano\LaravelCrudTemplates\FieldTypes\MorphManyType;
use JCSoriano\LaravelCrudTemplates\FieldTypes\MorphToManyType;
use JCSoriano\LaravelCrudTemplates\FieldTypes\MorphToType;
use JCSoriano\LaravelCrudTemplates\FieldTypes\StringType;
use JCSoriano\LaravelCrudTemplates\FieldTypes\TextType;
use JCSoriano\LaravelCrudTemplates\Generators\ApiRouteGenerator;
use JCSoriano\LaravelCrudTemplates\Generators\ControllerGenerator;
use JCSoriano\LaravelCrudTemplates\Generators\FactoryGenerator;
use JCSoriano\LaravelCrudTemplates\Generators\MigrationGenerator;
use JCSoriano\LaravelCrudTemplates\Generators\ModelGenerator;
use JCSoriano\LaravelCrudTemplates\Generators\PintGenerator;
use JCSoriano\LaravelCrudTemplates\Generators\PolicyGenerator;
use JCSoriano\LaravelCrudTemplates\Generators\ResourceGenerator;
use JCSoriano\LaravelCrudTemplates\Generators\StoreRequestGenerator;
use JCSoriano\LaravelCrudTemplates\Generators\TestGenerator;
use JCSoriano\LaravelCrudTemplates\Generators\UpdateRequestGenerator;
use JCSoriano\LaravelCrudTemplates\Printers\CastsPrinter;
use JCSoriano\LaravelCrudTemplates\Printers\FactoryPrinter;
use JCSoriano\LaravelCrudTemplates\Printers\FillablePrinter;
use JCSoriano\LaravelCrudTemplates\Printers\MigrationsPrinter;
use JCSoriano\LaravelCrudTemplates\Printers\RelationsPrinter;
use JCSoriano\LaravelCrudTemplates\Printers\ResourceOnlyPrinter;
use JCSoriano\LaravelCrudTemplates\Printers\ResourceRelationPrinter;
use JCSoriano\LaravelCrudTemplates\Printers\RulesPrinter;
use JCSoriano\LaravelCrudTemplates\Templates\ApiTemplate;
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

        $this->app->singleton(LaravelStub::class, function () {
            return new LaravelStub;
        });

        $this->registerFieldTypes();
        $this->registerGenerators();
        $this->registerTemplates();
        $this->registerPrinters();
    }

    protected function registerFieldTypes(): void
    {
        $fieldTypes = [
            'string' => StringType::class,
            'integer' => IntegerType::class,
            'decimal' => DecimalType::class,
            'date' => DateType::class,
            'datetime' => DateTimeType::class,
            'text' => TextType::class,
            'boolean' => BooleanType::class,
            'enum' => EnumType::class,
            'json' => JsonType::class,
            'belongsTo' => BelongsToType::class,
            'hasMany' => HasManyType::class,
            'belongsToMany' => BelongsToManyType::class,
            'morphTo' => MorphToType::class,
            'morphMany' => MorphManyType::class,
            'morphToMany' => MorphToManyType::class,
        ];

        foreach ($fieldTypes as $key => $class) {
            $this->app->bind("laravel-crud-templates::field-type::{$key}", $class);
        }
    }

    protected function registerGenerators(): void
    {
        $generators = [
            'controller' => ControllerGenerator::class,
            'model' => ModelGenerator::class,
            'policy' => PolicyGenerator::class,
            'store-request' => StoreRequestGenerator::class,
            'update-request' => UpdateRequestGenerator::class,
            'resource' => ResourceGenerator::class,
            'migration' => MigrationGenerator::class,
            'factory' => FactoryGenerator::class,
            'test' => TestGenerator::class,
            'api-route' => ApiRouteGenerator::class,
            'pint' => PintGenerator::class,
        ];

        foreach ($generators as $key => $class) {
            $this->app->bind("laravel-crud-templates::generator::{$key}", $class);
        }
    }

    protected function registerTemplates(): void
    {
        $templates = [
            'api' => ApiTemplate::class,
        ];

        foreach ($templates as $key => $class) {
            $this->app->bind("laravel-crud-templates::template::{$key}", $class);
        }
    }

    protected function registerPrinters(): void
    {
        $printers = [
            'casts' => CastsPrinter::class,
            'factory' => FactoryPrinter::class,
            'fillable' => FillablePrinter::class,
            'migrations' => MigrationsPrinter::class,
            'relations' => RelationsPrinter::class,
            'resource-only' => ResourceOnlyPrinter::class,
            'resource-relation' => ResourceRelationPrinter::class,
            'rules' => RulesPrinter::class,
        ];

        foreach ($printers as $key => $class) {
            $this->app->bind("laravel-crud-templates::printer::{$key}", $class);
        }
    }
}
