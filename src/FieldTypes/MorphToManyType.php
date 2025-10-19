<?php

namespace JCSoriano\LaravelCrudStubs\FieldTypes;

use JCSoriano\LaravelCrudStubs\DataObjects\Output;

class MorphToManyType extends FieldType
{
    public function relation(): Output
    {
        $name = $this->field->name;
        $relationName = $name->pluralCamelCase();
        $modelName = $name->studlyCase();

        $output = <<<OUTPUT
    public function {$relationName}(): MorphToMany
    {
        return \$this->morphToMany({$modelName}::class, 'taggable');
    }
OUTPUT;

        $namespaces = collect([
            "App\\Models\\{$modelName}",
            'Illuminate\\Database\\Eloquent\\Relations\\MorphToMany',
        ]);

        return new Output($output, $namespaces);
    }

    public function resourceRelations(): array
    {
        $name = $this->field->name;
        $fieldName = $name->pluralSnakeCase();
        $relationName = $name->pluralCamelCase();
        $resourceName = $name->studlyCase();

        return [
            $fieldName => "{$resourceName}Resource::collection(\$this->whenLoaded('{$relationName}'))",
        ];
    }
}
