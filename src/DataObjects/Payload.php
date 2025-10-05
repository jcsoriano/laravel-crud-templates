<?php

namespace JCSoriano\LaravelCrudStubs\DataObjects;

use Illuminate\Console\View\Components\Factory;
use Illuminate\Support\Collection;

class Payload
{
    public function __construct(
        public protected(set) Factory $components,
        public Model $model,
        public Collection $fields,
        public array $options = [],
        public array $variables = [],
        public array $conditions = [],
        public array $data = [],
    ) {}

    public function variables(): array
    {
        $model = $this->model;
        $name = $model->model();
        $namespace = $model->namespace();

        return [
            'MODEL' => $name->studlyCase(),
            'MODEL_CAMEL' => $name->camelCase(),
            'MODEL_SNAKE' => $name->snakeCase(),
            'MODEL_KEBAB' => $name->kebabCase(),
            'MODEL_CAMEL_PLURAL' => $name->pluralCamelCase(),
            'MODEL_SNAKE_PLURAL' => $name->pluralSnakeCase(),
            'MODEL_KEBAB_PLURAL' => $name->pluralKebabCase(),
            'NAMESPACE' => $namespace,
            'NAMESPACE_PATH' => $namespace ? '\\'.$namespace : '',
            ...$this->variables,
        ];
    }
}
