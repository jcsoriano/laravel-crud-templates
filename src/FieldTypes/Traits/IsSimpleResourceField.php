<?php

namespace JCSoriano\LaravelCrudTemplates\FieldTypes\Traits;

trait IsSimpleResourceField
{
    public function resourceOnly(): array
    {
        return [$this->field->name->snakeCase()];
    }
}
