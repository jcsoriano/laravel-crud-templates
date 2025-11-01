<?php

namespace JCSoriano\CrudTemplates\FieldTypes;

use JCSoriano\CrudTemplates\DataObjects\Field;

abstract class FieldType
{
    public function __construct(
        protected Field $field,
    ) {}
}
