<?php

namespace JCSoriano\CrudTemplates\FieldTypes;

use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\FieldTypes\Traits\ParsesRelatedModel;

class BelongsToManyType extends FieldType
{
    use ParsesRelatedModel;

    public function relation(): Output
    {
        $name = $this->field->name;
        $relationName = $name->pluralCamelCase();
        $modelName = $this->getModelName();
        $modelClass = $this->getModelClass();

        $output = <<<OUTPUT
    public function {$relationName}(): BelongsToMany
    {
        return \$this->belongsToMany({$modelName}::class)
            ->withTimestamps();
    }
OUTPUT;

        $namespaces = collect([
            $modelClass,
            'Illuminate\\Database\\Eloquent\\Relations\\BelongsToMany',
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
        $relatedModelName = $this->getModelName();

        // Sort model names alphabetically
        $models = [$currentModelName, $relatedModelName];
        sort($models);

        // Convert to singular snake_case and join
        $table1 = str($models[0])->snake()->singular();
        $table2 = str($models[1])->snake()->singular();

        return $table1.'_'.$table2;
    }

    public function createPivotTable(string $currentModelName): Output
    {
        $tableName = $this->pivotTableName($currentModelName);
        $relatedModelName = $this->getModelName();

        // Sort model names to determine foreign key order
        $models = [$currentModelName, $relatedModelName];
        sort($models);

        $foreignKey1 = str($models[0])->snake()->singular().'_id';
        $foreignKey2 = str($models[1])->snake()->singular().'_id';

        $table1 = str($models[0])->snake()->plural();
        $table2 = str($models[1])->snake()->plural();

        $output = <<<OUTPUT
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('{$foreignKey1}')->constrained('{$table1}')->cascadeOnDelete();
            \$table->foreignId('{$foreignKey2}')->constrained('{$table2}')->cascadeOnDelete();
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
