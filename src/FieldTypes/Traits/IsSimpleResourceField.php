<?php

namespace JCSoriano\LaravelCrudStubs\FieldTypes\Traits;

trait IsSimpleResourceField
{
    public function resourceOnly(): array
    {
        return [$this->field->name->snakeCase()];
    }
}
