<?php

namespace JCSoriano\CrudTemplates\FieldTypes;

use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\FieldTypes\Traits\ParsesRelatedModel;

class MorphManyType extends FieldType
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
    public function {$relationName}(): MorphMany
    {
        return \$this->morphMany({$modelName}::class, '{$morphName}');
    }
OUTPUT;

        $namespaces = collect([
            $modelClass,
            'Illuminate\\Database\\Eloquent\\Relations\\MorphMany',
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

    private function morphableName(): string
    {
        $field = $this->field;

        return $field->options['morphName']
            ?? str($field->name->singularSnakeCase())->finish('able');
    }
}
