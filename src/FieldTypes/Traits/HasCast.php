<?php

namespace JCSoriano\CrudTemplates\FieldTypes\Traits;

use JCSoriano\CrudTemplates\DataObjects\Output;

trait HasCast
{
    public function buildCast(string $type): Output
    {
        $output = "'{$this->field->name->snakeCase()}' => '{$type}'";

        return new Output($output);
    }
}
