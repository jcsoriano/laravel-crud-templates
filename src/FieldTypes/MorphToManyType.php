<?php

namespace JCSoriano\CrudTemplates\FieldTypes;

use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\FieldTypes\Traits\ParsesRelatedModel;

class MorphToManyType extends FieldType
{
    use ParsesRelatedModel;

    public function relation(): Output
    {
        $name = $this->field->name;
        $relationName = $name->pluralCamelCase();
        $modelName = $this->getModelName();
        $modelClass = $this->getModelClass();
        $morphName = $this->morphableName();

        $output = <<<OUTPUT
    public function {$relationName}(): MorphToMany
    {
        return \$this->morphToMany({$modelName}::class, '{$morphName}');
    }
OUTPUT;

        $namespaces = collect([
            $modelClass,
            'Illuminate\\Database\\Eloquent\\Relations\\MorphToMany',
        ]);

        return new Output($output, $namespaces);
    }

    public function resource(): array
    {
        $name = $this->field->name;
        $fieldName = $name->pluralSnakeCase();
        $relationName = $name->pluralCamelCase();
        $resourceName = $this->getModelName();

        return [
            $fieldName => "{$resourceName}Resource::collection(\$this->whenLoaded('{$relationName}'))",
        ];
    }

    public function pivotTableName(string $currentModelName = ''): string
    {
        return str($this->morphableName())->plural();
    }

    private function morphableName(): string
    {
        $field = $this->field;

        return $field->options['morphName']
            ?? str($field->name->singularSnakeCase())->finish('able');
    }

    public function createPivotTable(string $currentModelName = ''): Output
    {
        $tableName = $this->pivotTableName();
        $relatedModelName = $this->getModelName();
        $morphableName = $this->morphableName();

        // The related model foreign key
        $relatedForeignKey = str($relatedModelName)->snake()->singular().'_id';
        $relatedTable = str($relatedModelName)->snake()->plural();

        $output = <<<OUTPUT
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('{$relatedForeignKey}')->constrained('{$relatedTable}')->cascadeOnDelete();
            \$table->morphs('{$morphableName}');
            \$table->timestamps();
        });
OUTPUT;

        return new Output($output);
    }

    public function dropPivotTable(string $currentModelName = ''): Output
    {
        $tableName = $this->pivotTableName();
        $output = "Schema::dropIfExists('{$tableName}');";

        return new Output($output);
    }
}
