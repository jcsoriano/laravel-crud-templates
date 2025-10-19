<?php

namespace JCSoriano\LaravelCrudTemplates\FieldTypes;

use JCSoriano\LaravelCrudTemplates\DataObjects\Output;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\HasCast;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\HasSimpleMigration;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\HasSimpleRule;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\IsFillable;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\IsSimpleResourceField;

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

    public function factory(): string
    {
        return 'fake()->randomFloat(2, 0, 9999)';
    }
}
