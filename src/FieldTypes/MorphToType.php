<?php

namespace JCSoriano\LaravelCrudStubs\FieldTypes;

use JCSoriano\LaravelCrudStubs\DataObjects\Output;

class MorphToType extends FieldType
{
    public function migration(): Output
    {
        $output = "\$table->morphs('{$this->field->name->snakeCase()}');";

        return new Output($output);
    }

    public function relation(): Output
    {
        $relationName = $this->field->name->camelCase();

        $output = <<<OUTPUT
    public function {$relationName}(): MorphTo
    {
        return \$this->morphTo();
    }
OUTPUT;

        $namespaces = collect([
            'Illuminate\\Database\\Eloquent\\Relations\\MorphTo',
        ]);

        return new Output($output, $namespaces);
    }

    public function resourceRelations(): array
    {
        $fieldName = $this->field->name->snakeCase();
        $relationName = $this->field->name->camelCase();

        return [
            $fieldName => "\$this->whenLoaded('{$relationName}')",
        ];
    }

    public function fillable(): array
    {
        $fieldName = $this->field->name->snakeCase();

        return [
            "{$fieldName}_type",
            "{$fieldName}_id",
        ];
    }
}
