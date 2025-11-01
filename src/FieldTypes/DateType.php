<?php

namespace JCSoriano\CrudTemplates\FieldTypes;

use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasCast;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasSimpleMigration;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasSimpleRule;
use JCSoriano\CrudTemplates\FieldTypes\Traits\IsFillable;
use JCSoriano\CrudTemplates\FieldTypes\Traits\IsSimpleResourceField;

class DateType extends FieldType
{
    use HasCast;
    use HasSimpleMigration;
    use HasSimpleRule;
    use IsFillable;
    use IsSimpleResourceField;

    public function migration(): Output
    {
        return $this->buildSimpleMigration('date');
    }

    public function rule(): Output
    {
        return $this->buildSimpleRule('date');
    }

    public function cast(): Output
    {
        return $this->buildCast('immutable_date');
    }

    public function factory(): Output
    {
        return new Output('fake()->date()');
    }

    public function dbAssertion(): Output
    {
        $field = $this->field->name->snakeCase();

        return new Output(
            "'{$field}' => Carbon::parse(\$payload['{$field}'])->format('Y-m-d')",
            collect(['Illuminate\\Support\\Carbon']),
        );
    }
}
