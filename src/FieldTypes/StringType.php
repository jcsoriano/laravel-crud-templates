<?php

namespace JCSoriano\CrudTemplates\FieldTypes;

use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasSimpleMigration;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasSimpleRule;
use JCSoriano\CrudTemplates\FieldTypes\Traits\IsFillable;
use JCSoriano\CrudTemplates\FieldTypes\Traits\IsSimpleResourceField;

class StringType extends FieldType
{
    use HasSimpleMigration;
    use HasSimpleRule;
    use IsFillable;
    use IsSimpleResourceField;

    public function migration(): Output
    {
        return $this->buildSimpleMigration('string');
    }

    public function rule(): Output
    {
        return $this->buildSimpleRule(['string', 'max:255']);
    }

    public function fakeData(): Output
    {
        return new Output('fake()->words(3, true)');
    }
}
