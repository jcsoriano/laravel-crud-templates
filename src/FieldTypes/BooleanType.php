<?php

namespace JCSoriano\LaravelCrudTemplates\FieldTypes;

use JCSoriano\LaravelCrudTemplates\DataObjects\Output;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\HasCast;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\HasSimpleMigration;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\HasSimpleRule;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\IsFillable;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\IsSimpleResourceField;

class BooleanType extends FieldType
{
    use HasCast;
    use HasSimpleMigration;
    use HasSimpleRule;
    use IsFillable;
    use IsSimpleResourceField;

    public function migration(): Output
    {
        return $this->buildSimpleMigration('boolean');
    }

    public function rule(): Output
    {
        return $this->buildSimpleRule('boolean');
    }

    public function cast(): Output
    {
        return $this->buildCast('boolean');
    }

    public function factory(): string
    {
        return 'fake()->boolean()';
    }
}
