<?php

namespace JCSoriano\CrudTemplates\FieldTypes\Traits;

use JCSoriano\CrudTemplates\DataObjects\Output;

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
