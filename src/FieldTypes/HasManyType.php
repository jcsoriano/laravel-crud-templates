<?php

namespace JCSoriano\LaravelCrudStubs\FieldTypes;

use JCSoriano\LaravelCrudStubs\DataObjects\Output;

class HasManyType extends FieldType
{
    public function relation(): Output
    {
        $relationName = $this->field->name->pluralCamelCase();
        $modelName = $this->field->name->studlyCase();

        $output = <<<OUTPUT
    public function {$relationName}(): HasMany
    {
        return \$this->hasMany({$modelName}::class);
    }
OUTPUT;

        $namespaces = collect([
            "App\\Models\\{$modelName}",
            'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
        ]);

        return new Output($output, $namespaces);
    }

    public function resourceRelations(): array
    {
        $fieldName = $this->field->name->pluralSnakeCase();
        $relationName = $this->field->name->pluralCamelCase();
        $resourceName = $this->field->name->studlyCase();

        return [
            $fieldName => "{$resourceName}Resource::collection(\$this->whenLoaded('{$relationName}'))",
        ];
    }
}
