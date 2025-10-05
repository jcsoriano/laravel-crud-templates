<?php

namespace JCSoriano\LaravelCrudStubs\Pipelines;

use Illuminate\Console\View\Components\Factory;
use Illuminate\Pipeline\Pipeline as LaravelPipeline;
use Illuminate\Support\Collection;
use JCSoriano\LaravelCrudStubs\DataObjects\Model;
use JCSoriano\LaravelCrudStubs\DataObjects\Payload;
use JCSoriano\LaravelCrudStubs\Generators\Generator;
use JCSoriano\LaravelCrudStubs\LaravelCrudStubs;

abstract class Pipeline
{
    protected array $generators;

    protected Payload $payload;

    public function __construct(
        protected Model $model,
        protected Collection $fields,
        protected Factory $components,
        protected array $options,
    ) {
        $this->generators = LaravelCrudStubs::getGenerators();
        $this->payload = $this->buildPayload();
    }

    abstract public function pipeline(): array;

    protected function buildPayload(): Payload
    {
        return new Payload(
            model: $this->model,
            fields: $this->fields,
            components: $this->components,
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
            ->through($this->pipeline())
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

    protected function buildGenerator(string $generator): Generator
    {
        return app($this->generators[$generator]);
    }

    protected function buildGenerators(array $generators): array
    {
        return array_map(
            fn (string $generator): Generator => $this->buildGenerator($generator),
            $generators,
        );
    }
}
