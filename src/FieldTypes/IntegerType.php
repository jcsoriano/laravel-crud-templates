<?php

namespace JCSoriano\LaravelCrudStubs\FieldTypes;

use JCSoriano\LaravelCrudStubs\DataObjects\Output;
use JCSoriano\LaravelCrudStubs\FieldTypes\Traits\HasSimpleMigration;
use JCSoriano\LaravelCrudStubs\FieldTypes\Traits\HasSimpleRule;
use JCSoriano\LaravelCrudStubs\FieldTypes\Traits\IsFillable;
use JCSoriano\LaravelCrudStubs\FieldTypes\Traits\IsSimpleResourceField;

class IntegerType extends FieldType
{
    use HasSimpleMigration;
    use HasSimpleRule;
    use IsFillable;
    use IsSimpleResourceField;

    public function migration(): Output
    {
        return $this->buildSimpleMigration('integer');
    }

    public function rule(): Output
    {
        return $this->buildSimpleRule('integer');
    }

    public function factory(): string
    {
        return 'fake()->numberBetween(1, 1000)';
    }
}
