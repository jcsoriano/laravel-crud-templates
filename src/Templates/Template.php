<?php

namespace JCSoriano\LaravelCrudTemplates\Templates;

use Illuminate\Console\View\Components\Factory;
use Illuminate\Pipeline\Pipeline as LaravelPipeline;
use Illuminate\Support\Collection;
use JCSoriano\LaravelCrudTemplates\DataObjects\Model;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;
use JCSoriano\LaravelCrudTemplates\Generators\Generator;

abstract class Template
{
    protected Payload $payload;

    public function __construct(
        protected Model $model,
        protected Collection $fields,
        protected Factory $components,
        protected bool $force = false,
        protected ?string $table = null,
        protected array $options = [],
    ) {
        $this->payload = $this->buildPayload();
    }

    abstract public function template(): array;

    protected function buildPayload(): Payload
    {
        return new Payload(
            model: $this->model,
            fields: $this->fields,
            components: $this->components,
            force: $this->force,
            table: $this->table,
            options: $this->options,
            variables: $this->variables(),
            conditions: $this->conditions(),
            data: $this->data(),
        );
    }

    public function data(): array
    {
        return [];
    }

    public function run(): void
    {
        app(LaravelPipeline::class)
            ->send($this->payload)
            ->through($this->template())
            ->thenReturn();
    }

    protected function conditions(): array
    {
        return [];
    }

    protected function variables(): array
    {
        return [];
    }

    protected function buildGenerator(string|Generator $generator): Generator
    {
        // If it's already an instance, return it
        if ($generator instanceof Generator) {
            return $generator;
        }

        // If it's a class name, instantiate directly
        if (class_exists($generator)) {
            return app($generator);
        }

        // Otherwise, treat as a registered key and resolve from container binding
        return app("laravel-crud-templates::generator::{$generator}");
    }

    protected function buildGenerators(array $generators): array
    {
        return array_map(
            fn (string|Generator $generator): Generator => $this->buildGenerator($generator),
            $generators,
        );
    }
}
