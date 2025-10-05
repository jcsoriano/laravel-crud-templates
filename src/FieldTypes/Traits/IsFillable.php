<?php

namespace JCSoriano\LaravelCrudStubs\FieldTypes\Traits;

trait IsFillable
{
    public function fillable(): string
    {
        return $this->field->name->snakeCase();
    }
}
