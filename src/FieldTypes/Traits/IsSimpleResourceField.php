<?php

namespace JCSoriano\CrudTemplates\FieldTypes\Traits;

trait IsSimpleResourceField
{
    public function resourceOnly(): array
    {
        return [$this->field->name->snakeCase()];
    }
}
