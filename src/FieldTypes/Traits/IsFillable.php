<?php

namespace JCSoriano\CrudTemplates\FieldTypes\Traits;

use JCSoriano\CrudTemplates\DataObjects\Output;

trait IsFillable
{
    public function fillable(): string
    {
        return $this->field->name->snakeCase();
    }

    public function dbAssertion(): Output
    {
        $column = method_exists($this, 'column')
            ? $this->column()
            : $this->field->name->snakeCase();

        return new Output("'{$column}' => \$payload['{$column}']");
    }
}
