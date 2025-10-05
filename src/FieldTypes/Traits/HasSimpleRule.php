<?php

namespace JCSoriano\LaravelCrudStubs\FieldTypes\Traits;

use JCSoriano\LaravelCrudStubs\DataObjects\Output;

trait HasSimpleRule
{
    protected function buildSimpleRule(string $rule): Output
    {
        $required = $this->field->required ? 'required' : 'nullable';
        $output = "'{$this->field->name->snakeCase()}' => '{$required}|{$rule}'";

        return new Output($output);
    }
}
