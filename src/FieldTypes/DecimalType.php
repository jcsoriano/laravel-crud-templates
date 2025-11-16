<?php

namespace JCSoriano\CrudTemplates\FieldTypes;

use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasCast;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasSimpleMigration;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasSimpleRule;
use JCSoriano\CrudTemplates\FieldTypes\Traits\IsFillable;
use JCSoriano\CrudTemplates\FieldTypes\Traits\IsSimpleResourceField;

class DecimalType extends FieldType
{
    use HasCast;
    use HasSimpleMigration;
    use HasSimpleRule;
    use IsFillable;
    use IsSimpleResourceField;

    public function migration(): Output
    {
        return $this->buildSimpleMigration('decimal');
    }

    public function rule(): Output
    {
        return $this->buildSimpleRule('numeric');
    }

    public function cast(): Output
    {
        return $this->buildCast('decimal:2');
    }

    public function fakeData(): Output
    {
        return new Output('fake()->randomFloat(2, 0, 9999)');
    }
}
