<?php

namespace JCSoriano\CrudTemplates\FieldTypes;

use JCSoriano\CrudTemplates\DataObjects\Name;
use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\FieldTypes\Traits\ParsesRelatedModel;

class BelongsToType extends FieldType
{
    use ParsesRelatedModel;

    public function column(): string
    {
        return $this->field->name->snakeCase().'_id';
    }

    private function getTableName(): string
    {
        $modelName = $this->getModelName();

        return (new Name($modelName))->pluralSnakeCase();
    }

    public function migration(): Output
    {
        $table = $this->getTableName();
        $output = "\$table->foreignId('{$this->column()}')->constrained('{$table}');";

        return new Output($output);
    }

    public function rule(): Output
    {
        $table = $this->getTableName();
        $required = $this->field->required ? 'required' : 'nullable';
        $output = "'{$this->column()}' => ['bail', '{$required}', 'exists:{$table},id']";

        return new Output($output);
    }

    public function relation(): Output
    {
        $name = $this->field->name;
        $relationName = $name->camelCase();
        $modelName = $this->getModelName();
        $modelClass = $this->getModelClass();

        $output = <<<OUTPUT
    public function {$relationName}(): BelongsTo
    {
        return \$this->belongsTo({$modelName}::class);
    }
OUTPUT;

        $namespaces = collect([
            $modelClass,
            'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
        ]);

        return new Output($output, $namespaces);
    }

    public function resource(): array
    {
        $name = $this->field->name;
        $fieldName = $name->snakeCase();
        $relationName = $name->camelCase();
        $resourceName = $this->getModelName();

        return [
            $fieldName => "{$resourceName}Resource::make(\$this->whenLoaded('{$relationName}'))",
        ];
    }

    public function fillable(): string
    {
        return $this->column();
    }

    public function fakeData(): Output
    {
        $modelName = $this->getModelName();
        $modelClass = $this->getModelClass();
        $output = "{$modelName}::factory()";

        return new Output($output, collect([$modelClass]));
    }
}
