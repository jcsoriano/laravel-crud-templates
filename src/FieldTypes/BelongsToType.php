<?php

namespace JCSoriano\LaravelCrudTemplates\FieldTypes;

use Illuminate\Support\Str;
use JCSoriano\LaravelCrudTemplates\DataObjects\Output;

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
        $output = "'{$this->column()}' => ['bail', '{$required}', 'exists:{$table},id']";

        return new Output($output);
    }

    public function relation(): Output
    {
        $name = $this->field->name;
        $relationName = $name->camelCase();
        $modelName = $name->studlyCase();

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
        $name = $this->field->name;
        $fieldName = $name->snakeCase();
        $relationName = $name->camelCase();
        $resourceName = $name->studlyCase();

        return [
            $fieldName => "{$resourceName}Resource::make(\$this->whenLoaded('{$relationName}'))",
        ];
    }

    public function fillable(): string
    {
        return $this->column();
    }

    public function factory(): Output
    {
        $modelName = $this->field->name->studlyCase();
        $output = "{$modelName}::factory()";

        return new Output($output, collect(["App\\Models\\{$modelName}"]));
    }
}
