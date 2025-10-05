<?php

namespace JCSoriano\LaravelCrudStubs\FieldTypes;

use Illuminate\Support\Str;
use JCSoriano\LaravelCrudStubs\DataObjects\Output;

class BelongsToType extends FieldType
{
    private function column(): string
    {
        return $this->field->name->snakeCase().'_id';
    }

    public function migration(): Output
    {
        $table = Str::plural(Str::snake($this->field->name->name));
        $output = "\$table->foreignId('{$this->column()}')->constrained('{$table}');";

        return new Output($output);
    }

    public function rule(): Output
    {
        $table = Str::plural(Str::snake($this->field->name->name));
        $required = $this->field->required ? 'required' : 'nullable';
        $output = "'{$this->column()}' => '{$required}|exists:{$table},id'";

        return new Output($output);
    }

    public function relation(): Output
    {
        $relationName = $this->field->name->camelCase();
        $modelName = $this->field->name->studlyCase();

        $output = <<<OUTPUT
    public function {$relationName}(): BelongsTo
    {
        return \$this->belongsTo({$modelName}::class);
    }
OUTPUT;

        $namespaces = collect([
            "App\\Models\\{$modelName}",
            'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
        ]);

        return new Output($output, $namespaces);
    }

    public function resourceRelations(): array
    {
        $fieldName = $this->field->name->snakeCase();
        $relationName = $this->field->name->camelCase();
        $resourceName = $this->field->name->studlyCase();

        return [
            $fieldName => "{$resourceName}Resource::make(\$this->whenLoaded('{$relationName}'))",
        ];
    }

    public function fillable(): string
    {
        return $this->column();
    }

    public function factory(): string
    {
        $modelName = $this->field->name->studlyCase();

        return "{$modelName}::factory()";
    }
}
