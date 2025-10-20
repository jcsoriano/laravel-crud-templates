<?php

namespace JCSoriano\LaravelCrudTemplates\FieldTypes;

use JCSoriano\LaravelCrudTemplates\DataObjects\Output;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\HasCast;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\HasSimpleMigration;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\HasSimpleRule;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\IsFillable;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\IsSimpleResourceField;

class DateTimeType extends FieldType
{
    use HasCast,
        HasSimpleMigration,
        HasSimpleRule,
        IsFillable,
        IsSimpleResourceField;

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

    public function factory(): Output
    {
        return new Output('fake()->dateTime()');
    }

    public function dbAssertion(): Output
    {
        $field = $this->field->name->snakeCase();

        return new Output(
            "'{$field}' => Carbon::parse(\$payload['{$field}'])->format('Y-m-d H:i:s')",
            collect(['Illuminate\\Support\\Carbon']),
        );
    }
}
