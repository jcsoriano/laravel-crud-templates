<?php

namespace JCSoriano\CrudTemplates;

use JCSoriano\CrudTemplates\Commands\GenerateCrud;
use JCSoriano\CrudTemplates\Commands\MakeFieldTypeCommand;
use JCSoriano\CrudTemplates\Commands\MakeGeneratorCommand;
use JCSoriano\CrudTemplates\Commands\MakePrinterCommand;
use JCSoriano\CrudTemplates\Commands\MakeTemplateCommand;
use JCSoriano\CrudTemplates\FieldTypes\BelongsToManyType;
use JCSoriano\CrudTemplates\FieldTypes\BelongsToType;
use JCSoriano\CrudTemplates\FieldTypes\BooleanType;
use JCSoriano\CrudTemplates\FieldTypes\DateTimeType;
use JCSoriano\CrudTemplates\FieldTypes\DateType;
use JCSoriano\CrudTemplates\FieldTypes\DecimalType;
use JCSoriano\CrudTemplates\FieldTypes\EnumType;
use JCSoriano\CrudTemplates\FieldTypes\HasManyType;
use JCSoriano\CrudTemplates\FieldTypes\IntegerType;
use JCSoriano\CrudTemplates\FieldTypes\JsonType;
use JCSoriano\CrudTemplates\FieldTypes\MorphManyType;
use JCSoriano\CrudTemplates\FieldTypes\MorphToManyType;
use JCSoriano\CrudTemplates\FieldTypes\MorphToType;
use JCSoriano\CrudTemplates\FieldTypes\StringType;
use JCSoriano\CrudTemplates\FieldTypes\TextType;
use JCSoriano\CrudTemplates\Generators\ApiRouteGenerator;
use JCSoriano\CrudTemplates\Generators\ControllerGenerator;
use JCSoriano\CrudTemplates\Generators\FactoryGenerator;
use JCSoriano\CrudTemplates\Generators\MigrationGenerator;
use JCSoriano\CrudTemplates\Generators\ModelGenerator;
use JCSoriano\CrudTemplates\Generators\PintGenerator;
use JCSoriano\CrudTemplates\Generators\PivotMigrationGenerator;
use JCSoriano\CrudTemplates\Generators\PolicyGenerator;
use JCSoriano\CrudTemplates\Generators\ResourceGenerator;
use JCSoriano\CrudTemplates\Generators\StoreRequestGenerator;
use JCSoriano\CrudTemplates\Generators\TestGenerator;
use JCSoriano\CrudTemplates\Generators\UpdateRequestGenerator;
use JCSoriano\CrudTemplates\Printers\CastsPrinter;
use JCSoriano\CrudTemplates\Printers\CreatePivotTablePrinter;
use JCSoriano\CrudTemplates\Printers\DbAssertionPrinter;
use JCSoriano\CrudTemplates\Printers\DropPivotTablePrinter;
use JCSoriano\CrudTemplates\Printers\FactoryPrinter;
use JCSoriano\CrudTemplates\Printers\FillablePrinter;
use JCSoriano\CrudTemplates\Printers\MigrationsPrinter;
use JCSoriano\CrudTemplates\Printers\RelationsPrinter;
use JCSoriano\CrudTemplates\Printers\ResourcePrinter;
use JCSoriano\CrudTemplates\Printers\RulesPrinter;
use JCSoriano\CrudTemplates\Templates\ApiTemplate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CrudTemplatesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('crud-templates')
            ->hasCommands([
                GenerateCrud::class,
                MakeGeneratorCommand::class,
                MakePrinterCommand::class,
                MakeFieldTypeCommand::class,
                MakeTemplateCommand::class,
            ]);
    }

    public function packageBooted(): void
    {
        // Publish stubs
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/stubs/api' => base_path('stubs/api'),
            ], 'crud-templates-stubs');

            // Publish make command stubs
            $this->publishes([
                __DIR__.'/stubs/crud-templates' => base_path('stubs/crud-templates'),
            ], 'crud-templates-make-stubs');

            // Publish service provider
            $this->publishes([
                __DIR__.'/stubs/CrudTemplatesServiceProvider.stub' => app_path('Providers/CrudTemplatesServiceProvider.php'),
            ], 'crud-templates-provider');
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
            // Bind as class name string. Field types are instantiated later by parsers
            // when they have the required Field data. This prevents unresolvable
            // dependency errors when resolving from the container.
            $this->app->bind("crud-templates::field-type::{$key}", fn () => $class);
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
            'pivot-migration' => PivotMigrationGenerator::class,
            'factory' => FactoryGenerator::class,
            'test' => TestGenerator::class,
            'api-route' => ApiRouteGenerator::class,
            'pint' => PintGenerator::class,
        ];

        foreach ($generators as $key => $class) {
            $this->app->bind("crud-templates::generator::{$key}", $class);
        }
    }

    protected function registerTemplates(): void
    {
        $templates = [
            'api' => ApiTemplate::class,
        ];

        foreach ($templates as $key => $class) {
            // Bind as class name string. Templates are instantiated by the command
            // with the required parameters (model, fields, etc.). This prevents
            // unresolvable dependency errors when resolving from the container.
            $this->app->bind("crud-templates::template::{$key}", fn () => $class);
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
            'resource' => ResourcePrinter::class,
            'rules' => RulesPrinter::class,
            'dbAssertions' => DbAssertionPrinter::class,
            'createPivotTables' => CreatePivotTablePrinter::class,
            'dropPivotTables' => DropPivotTablePrinter::class,
        ];

        foreach ($printers as $key => $class) {
            $this->app->bind("crud-templates::printer::{$key}", $class);
        }
    }
}
