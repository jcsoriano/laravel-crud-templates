<?php

namespace JCSoriano\CrudTemplates\FieldTypes;

use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasSimpleMigration;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasSimpleRule;
use JCSoriano\CrudTemplates\FieldTypes\Traits\IsFillable;
use JCSoriano\CrudTemplates\FieldTypes\Traits\IsSimpleResourceField;

class TextType extends FieldType
{
    use HasSimpleMigration;
    use HasSimpleRule;
    use IsFillable;
    use IsSimpleResourceField;

    public function migration(): Output
    {
        return $this->buildSimpleMigration('text');
    }

    public function rule(): Output
    {
        return $this->buildSimpleRule('string');
    }

    public function fakeData(): Output
    {
        return new Output('fake()->paragraph()');
    }
}
