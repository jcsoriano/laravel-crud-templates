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

        $output = <<<OUTPUT
    public function {$relationName}(): MorphToMany
    {
        return \$this->morphToMany({$modelName}::class, '{$relationName}');
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

    public function pivotTableName(string $currentModelName): string
    {
        return $this->field->name->pluralSnakeCase();
    }

    public function createPivotTable(string $currentModelName): Output
    {
        $tableName = $this->pivotTableName($currentModelName);
        $name = $this->field->name;
        $relatedModelName = $this->getModelName();
        
        // The morphable name matches what's used in the relation method (pluralCamelCase)
        $morphableName = $name->pluralCamelCase();
        
        // The related model foreign key
        $relatedForeignKey = str($relatedModelName)->snake()->singular().'_id';
        $relatedTable = str($relatedModelName)->snake()->plural();

        $output = <<<OUTPUT
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->morphs('{$morphableName}');
            \$table->foreignId('{$relatedForeignKey}')->constrained('{$relatedTable}')->cascadeOnDelete();
            \$table->timestamps();
        });
OUTPUT;

        return new Output($output);
    }

    public function dropPivotTable(string $currentModelName): Output
    {
        $tableName = $this->pivotTableName($currentModelName);
        $output = "Schema::dropIfExists('{$tableName}');";

        return new Output($output);
    }
}
