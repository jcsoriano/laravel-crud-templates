<?php

namespace JCSoriano\LaravelCrudTemplates\FieldTypes;

use JCSoriano\LaravelCrudTemplates\DataObjects\Output;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\HasSimpleMigration;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\HasSimpleRule;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\IsFillable;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\IsSimpleResourceField;

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
        return $this->buildSimpleRule('string|max:255');
    }

    public function factory(): Output
    {
        return new Output('fake()->words(3, true)');
    }
}
