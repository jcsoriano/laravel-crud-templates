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
        $nullable = $this->field->required ? '' : '->nullable()';
        $output = "\$table->string('{$this->field->name->snakeCase()}'){$nullable};";

        return new Output($output);
    }

    public function cast(): Output
    {
        $enumClass = $this->field->options['enumClass'] ?? 'Enum';
        $output = "'{$this->field->name->snakeCase()}' => {$enumClass}::class";

        return new Output($output, collect(["App\\Enums\\{$enumClass}"]));
    }

    public function factory(): string
    {
        $enumClass = $this->field->options['enumClass'] ?? 'Enum';

        return "{$enumClass}::cases()[array_rand({$enumClass}::cases())]";
    }
}
