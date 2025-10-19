<?php

namespace JCSoriano\LaravelCrudTemplates\FieldTypes\Traits;

use JCSoriano\LaravelCrudTemplates\DataObjects\Output;

trait HasCast
{
    public function buildCast(string $type): Output
    {
        $output = "'{$this->field->name->snakeCase()}' => '{$type}'";

        return new Output($output);
    }
}
