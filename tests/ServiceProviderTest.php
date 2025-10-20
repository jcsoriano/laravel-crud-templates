<?php

use JCSoriano\LaravelCrudTemplates\FieldTypes\StringType;

it('can resolve field type bindings without dependency errors', function () {
    $stringTypeClass = app('laravel-crud-templates::field-type::string');

    expect($stringTypeClass)->toBe(StringType::class);
});

it('can resolve all field type bindings', function () {
    $types = [
        'string',
        'integer',
        'decimal',
        'date',
        'datetime',
        'text',
        'boolean',
        'enum',
        'json',
        'belongsTo',
        'hasMany',
        'belongsToMany',
        'morphTo',
        'morphMany',
        'morphToMany',
    ];

    foreach ($types as $type) {
        $class = app("laravel-crud-templates::field-type::{$type}");
        expect($class)->toBeString();
        expect(class_exists($class))->toBeTrue();
    }
});

it('can resolve generator bindings', function () {
    $generators = [
        'controller',
        'model',
        'policy',
        'store-request',
        'update-request',
        'resource',
        'migration',
        'factory',
        'test',
        'api-route',
        'pint',
    ];

    foreach ($generators as $generator) {
        $instance = app("laravel-crud-templates::generator::{$generator}");
        expect($instance)->toBeInstanceOf(\JCSoriano\LaravelCrudTemplates\Generators\Generator::class);
    }
});

it('can resolve template bindings', function () {
    $templateClass = app('laravel-crud-templates::template::api');
    expect($templateClass)->toBeString();
    expect(class_exists($templateClass))->toBeTrue();
    expect(is_subclass_of($templateClass, \JCSoriano\LaravelCrudTemplates\Templates\Template::class))->toBeTrue();
});

it('can resolve printer bindings', function () {
    $printers = [
        'casts',
        'factory',
        'fillable',
        'migrations',
        'relations',
        'resource-only',
        'resource-relation',
        'rules',
    ];

    foreach ($printers as $printer) {
        $instance = app("laravel-crud-templates::printer::{$printer}");
        expect($instance)->toBeInstanceOf(\JCSoriano\LaravelCrudTemplates\Printers\Printer::class);
    }
});
