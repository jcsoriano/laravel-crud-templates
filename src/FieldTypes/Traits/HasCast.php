<?php

namespace JCSoriano\LaravelCrudStubs\FieldTypes\Traits;

use JCSoriano\LaravelCrudStubs\DataObjects\Output;

trait HasCast
{
    public function buildCast(string $type): Output
    {
        $output = "'{$this->field->name->snakeCase()}' => '{$type}'";

        return new Output($output);
    }
}
