<?php

namespace JCSoriano\LaravelCrudStubs\FieldTypes\Traits;

use JCSoriano\LaravelCrudStubs\DataObjects\Output;

trait HasSimpleMigration
{
    public function buildSimpleMigration(string $type): Output
    {
        $nullable = $this->field->required ? '' : '->nullable()';
        $output = "\$table->{$type}('{$this->field->name->snakeCase()}'){$nullable};";

        return new Output($output);
    }
}
