<?php

namespace JCSoriano\LaravelCrudTemplates\FieldTypes\Traits;

use JCSoriano\LaravelCrudTemplates\DataObjects\Output;

trait HasSimpleMigration
{
    public function buildSimpleMigration(string $type): Output
    {
        $field = $this->field;
        $nullable = $field->required ? '' : '->nullable()';
        $output = "\$table->{$type}('{$field->name->snakeCase()}'){$nullable};";

        return new Output($output);
    }
}
