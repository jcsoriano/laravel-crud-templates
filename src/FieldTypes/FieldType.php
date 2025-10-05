<?php

namespace JCSoriano\LaravelCrudStubs\FieldTypes;

use JCSoriano\LaravelCrudStubs\DataObjects\Field;

abstract class FieldType
{
    public function __construct(
        protected Field $field,
    ) {}
}
