<?php

namespace JCSoriano\CrudTemplates\FieldTypes;

use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasCast;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasSimpleMigration;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasSimpleRule;
use JCSoriano\CrudTemplates\FieldTypes\Traits\IsFillable;
use JCSoriano\CrudTemplates\FieldTypes\Traits\IsSimpleResourceField;

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

    public function fakeData(): Output
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
