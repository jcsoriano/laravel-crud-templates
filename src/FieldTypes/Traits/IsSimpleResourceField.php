<?php

namespace JCSoriano\CrudTemplates\FieldTypes\Traits;

trait IsSimpleResourceField
{
    public function resource(): array
    {
        $fieldName = $this->field->name->snakeCase();

        return [$fieldName => "\$this->{$fieldName}"];
    }
}
