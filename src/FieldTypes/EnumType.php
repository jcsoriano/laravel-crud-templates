<?php

namespace JCSoriano\LaravelCrudStubs\FieldTypes;

use JCSoriano\LaravelCrudStubs\DataObjects\Output;
use JCSoriano\LaravelCrudStubs\FieldTypes\Traits\IsFillable;
use JCSoriano\LaravelCrudStubs\FieldTypes\Traits\IsSimpleResourceField;

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

    public function factory(): string
    {
        $enumClass = $this->field->options['enumClass'] ?? 'Enum';

        return "{$enumClass}::cases()[array_rand({$enumClass}::cases())]";
    }
}
