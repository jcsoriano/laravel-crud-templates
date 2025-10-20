<?php

namespace JCSoriano\LaravelCrudTemplates\FieldTypes\Traits;

use Illuminate\Support\Arr;
use JCSoriano\LaravelCrudTemplates\DataObjects\Output;

trait HasSimpleRule
{
    protected function buildSimpleRule(array|string $rules): Output
    {
        $field = $this->field;
        $required = $field->required ? 'required' : 'nullable';
        $rules = [$required, ...Arr::wrap($rules)];
        $rulesString = "'".implode("', '", $rules)."'";
        $output = "'{$field->name->snakeCase()}' => [{$rulesString}]";

        return new Output($output);
    }
}
