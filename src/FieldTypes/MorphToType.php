<?php

namespace JCSoriano\CrudTemplates\FieldTypes;

use JCSoriano\CrudTemplates\DataObjects\Output;

class MorphToType extends FieldType
{
    public function migration(): Output
    {
        $method = $this->field->required ? 'morphs' : 'nullableMorphs';
        $output = "\$table->{$method}('{$this->field->name->snakeCase()}');";

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

    public function resource(): array
    {
        $name = $this->field->name;
        $fieldName = $name->snakeCase();
        $relationName = $name->camelCase();

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
