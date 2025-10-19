<?php

namespace JCSoriano\LaravelCrudStubs\FieldTypes\Traits;

use JCSoriano\LaravelCrudStubs\DataObjects\Output;

trait HasSimpleRule
{
    protected function buildSimpleRule(string $rule): Output
    {
        $field = $this->field;
        $required = $field->required ? 'required' : 'nullable';
        $output = "'{$field->name->snakeCase()}' => '{$required}|{$rule}'";

        return new Output($output);
    }
}
