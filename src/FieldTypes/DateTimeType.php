<?php

namespace JCSoriano\LaravelCrudStubs\FieldTypes;

use JCSoriano\LaravelCrudStubs\DataObjects\Output;
use JCSoriano\LaravelCrudStubs\FieldTypes\Traits\HasCast;
use JCSoriano\LaravelCrudStubs\FieldTypes\Traits\HasSimpleMigration;
use JCSoriano\LaravelCrudStubs\FieldTypes\Traits\HasSimpleRule;
use JCSoriano\LaravelCrudStubs\FieldTypes\Traits\IsFillable;
use JCSoriano\LaravelCrudStubs\FieldTypes\Traits\IsSimpleResourceField;

class DateTimeType extends FieldType
{
    use HasCast;
    use HasSimpleMigration;
    use HasSimpleRule;
    use IsFillable;
    use IsSimpleResourceField;

    public function migration(): Output
    {
        return $this->buildSimpleMigration('dateTime');
    }

    public function rule(): Output
    {
        return $this->buildSimpleRule('date');
    }

    public function cast(): Output
    {
        return $this->buildCast('immutable_datetime');
    }

    public function factory(): string
    {
        return 'fake()->dateTime()';
    }
}
