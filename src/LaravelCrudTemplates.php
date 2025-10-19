<?php

namespace JCSoriano\LaravelCrudTemplates;

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

class LaravelCrudTemplates
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

    protected static array $templates = [
        'api' => ApiTemplate::class,
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

    public static function registerTemplate(string $key, string $class): void
    {
        static::$templates[$key] = $class;
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

    public static function getTemplates(): array
    {
        return static::$templates;
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
