<?php

namespace JCSoriano\LaravelCrudTemplates\FieldTypes\Traits;

use JCSoriano\LaravelCrudTemplates\DataObjects\Output;

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
