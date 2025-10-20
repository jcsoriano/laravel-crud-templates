<?php

namespace JCSoriano\LaravelCrudTemplates\FieldTypes;

use JCSoriano\LaravelCrudTemplates\DataObjects\Output;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\IsFillable;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\IsSimpleResourceField;

class EnumType extends FieldType
{
    use IsFillable;
    use IsSimpleResourceField;

    public function migration(): Output
    {
        $field = $this->field;
        $nullable = $field->required ? '' : '->nullable()';
        $output = "\$table->string('{$field->name->snakeCase()}'){$nullable};";

        return new Output($output);
    }

    public function cast(): Output
    {
        $field = $this->field;
        $enumClass = $field->options['enumClass'] ?? 'Enum';
        $output = "'{$field->name->snakeCase()}' => {$enumClass}::class";

        return new Output($output, collect(["App\\Enums\\{$enumClass}"]));
    }

    public function rule(): Output
    {
        $field = $this->field;
        $enumClass = $field->options['enumClass'] ?? 'Enum';
        $required = $field->required ? 'required' : 'nullable';
        $output = "'{$field->name->snakeCase()}' => ['bail', '{$required}', Rule::enum({$enumClass}::class)]";

        return new Output($output, collect([
            "App\\Enums\\{$enumClass}",
            'Illuminate\\Validation\\Rule',
        ]));
    }

    public function factory(): Output
    {
        $enumClass = $this->field->options['enumClass'] ?? 'Enum';
        $output = "{$enumClass}::cases()[array_rand({$enumClass}::cases())]";

        return new Output($output, collect(["App\\Enums\\{$enumClass}"]));
    }
}
