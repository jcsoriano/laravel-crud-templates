<?php

namespace JCSoriano\LaravelCrudStubs;

use JCSoriano\LaravelCrudStubs\FieldTypes\BelongsToManyType;
use JCSoriano\LaravelCrudStubs\FieldTypes\BelongsToType;
use JCSoriano\LaravelCrudStubs\FieldTypes\BooleanType;
use JCSoriano\LaravelCrudStubs\FieldTypes\DateTimeType;
use JCSoriano\LaravelCrudStubs\FieldTypes\DateType;
use JCSoriano\LaravelCrudStubs\FieldTypes\DecimalType;
use JCSoriano\LaravelCrudStubs\FieldTypes\EnumType;
use JCSoriano\LaravelCrudStubs\FieldTypes\HasManyType;
use JCSoriano\LaravelCrudStubs\FieldTypes\IntegerType;
use JCSoriano\LaravelCrudStubs\FieldTypes\JsonType;
use JCSoriano\LaravelCrudStubs\FieldTypes\MorphManyType;
use JCSoriano\LaravelCrudStubs\FieldTypes\MorphToManyType;
use JCSoriano\LaravelCrudStubs\FieldTypes\MorphToType;
use JCSoriano\LaravelCrudStubs\FieldTypes\StringType;
use JCSoriano\LaravelCrudStubs\FieldTypes\TextType;
use JCSoriano\LaravelCrudStubs\Generators\ApiRouteGenerator;
use JCSoriano\LaravelCrudStubs\Generators\ControllerGenerator;
use JCSoriano\LaravelCrudStubs\Generators\FactoryGenerator;
use JCSoriano\LaravelCrudStubs\Generators\MigrationGenerator;
use JCSoriano\LaravelCrudStubs\Generators\ModelGenerator;
use JCSoriano\LaravelCrudStubs\Generators\PintGenerator;
use JCSoriano\LaravelCrudStubs\Generators\PolicyGenerator;
use JCSoriano\LaravelCrudStubs\Generators\ResourceGenerator;
use JCSoriano\LaravelCrudStubs\Generators\StoreRequestGenerator;
use JCSoriano\LaravelCrudStubs\Generators\TestGenerator;
use JCSoriano\LaravelCrudStubs\Generators\UpdateRequestGenerator;
use JCSoriano\LaravelCrudStubs\Pipelines\ApiPipeline;
use JCSoriano\LaravelCrudStubs\Printers\CastsPrinter;
use JCSoriano\LaravelCrudStubs\Printers\FactoryPrinter;
use JCSoriano\LaravelCrudStubs\Printers\FillablePrinter;
use JCSoriano\LaravelCrudStubs\Printers\MigrationsPrinter;
use JCSoriano\LaravelCrudStubs\Printers\RelationsPrinter;
use JCSoriano\LaravelCrudStubs\Printers\ResourceOnlyPrinter;
use JCSoriano\LaravelCrudStubs\Printers\ResourceRelationPrinter;
use JCSoriano\LaravelCrudStubs\Printers\RulesPrinter;

class LaravelCrudStubs
{
    protected static array $fieldTypes = [
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

    protected static array $generators = [
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

    protected static array $pipelines = [
        'api' => ApiPipeline::class,
    ];

    protected static array $printers = [
        'casts' => CastsPrinter::class,
        'factory' => FactoryPrinter::class,
        'fillable' => FillablePrinter::class,
        'migrations' => MigrationsPrinter::class,
        'relations' => RelationsPrinter::class,
        'resource-only' => ResourceOnlyPrinter::class,
        'resource-relation' => ResourceRelationPrinter::class,
        'rules' => RulesPrinter::class,
    ];

    public static function registerFieldType(string $key, string $class): void
    {
        static::$fieldTypes[$key] = $class;
    }

    public static function registerGenerator(string $key, string $class): void
    {
        static::$generators[$key] = $class;
    }

    public static function registerPipeline(string $key, string $class): void
    {
        static::$pipelines[$key] = $class;
    }

    public static function registerPrinter(string $key, string $class): void
    {
        static::$printers[$key] = $class;
    }

    public static function getFieldTypes(): array
    {
        return static::$fieldTypes;
    }

    public static function getGenerators(): array
    {
        return static::$generators;
    }

    public static function getPipelines(): array
    {
        return static::$pipelines;
    }

    public static function getPrinters(): array
    {
        return static::$printers;
    }

    public static function buildPrinter(string $printer): object
    {
        return app(static::$printers[$printer]);
    }
}
