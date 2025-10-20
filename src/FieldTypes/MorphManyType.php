<?php

namespace JCSoriano\LaravelCrudTemplates\FieldTypes;

use JCSoriano\LaravelCrudTemplates\DataObjects\Output;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\ParsesRelatedModel;

class MorphManyType extends FieldType
{
    use ParsesRelatedModel;

    public function relation(): Output
    {
        $name = $this->field->name;
        $relationName = $name->pluralCamelCase();
        $modelName = $this->getModelName();
        $modelClass = $this->getModelClass();

        $output = <<<OUTPUT
    public function {$relationName}(): MorphMany
    {
        return \$this->morphMany({$modelName}::class, 'morphable');
    }
OUTPUT;

        $namespaces = collect([
            $modelClass,
            'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
        ]);

        return new Output($output, $namespaces);
    }

    public function resourceRelations(): array
    {
        $name = $this->field->name;
        $fieldName = $name->pluralSnakeCase();
        $relationName = $name->pluralCamelCase();
        $resourceName = $this->getModelName();

        return [
            $fieldName => "{$resourceName}Resource::collection(\$this->whenLoaded('{$relationName}'))",
        ];
    }
}
