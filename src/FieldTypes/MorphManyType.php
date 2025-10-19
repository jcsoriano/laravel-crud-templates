<?php

namespace JCSoriano\LaravelCrudTemplates\FieldTypes;

use JCSoriano\LaravelCrudTemplates\DataObjects\Output;

class MorphManyType extends FieldType
{
    public function relation(): Output
    {
        $name = $this->field->name;
        $relationName = $name->pluralCamelCase();
        $modelName = $name->studlyCase();

        $output = <<<OUTPUT
    public function {$relationName}(): MorphMany
    {
        return \$this->morphMany({$modelName}::class, 'morphable');
    }
OUTPUT;

        $namespaces = collect([
            "App\\Models\\{$modelName}",
            'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
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
