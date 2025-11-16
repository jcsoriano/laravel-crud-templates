<?php

namespace JCSoriano\CrudTemplates\FieldTypes;

use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasSimpleMigration;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasSimpleRule;
use JCSoriano\CrudTemplates\FieldTypes\Traits\IsFillable;
use JCSoriano\CrudTemplates\FieldTypes\Traits\IsSimpleResourceField;

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

    public function fakeData(): Output
    {
        return new Output('fake()->numberBetween(1, 1000)');
    }
}
