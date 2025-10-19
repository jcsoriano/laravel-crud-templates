<?php

namespace JCSoriano\LaravelCrudTemplates\FieldTypes;

use JCSoriano\LaravelCrudTemplates\DataObjects\Field;

abstract class FieldType
{
    public function __construct(
        protected Field $field,
    ) {}
}
