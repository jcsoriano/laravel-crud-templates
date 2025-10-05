<?php

namespace JCSoriano\LaravelCrudStubs\FieldTypes;

use JCSoriano\LaravelCrudStubs\DataObjects\Output;
use JCSoriano\LaravelCrudStubs\FieldTypes\Traits\HasSimpleMigration;
use JCSoriano\LaravelCrudStubs\FieldTypes\Traits\HasSimpleRule;
use JCSoriano\LaravelCrudStubs\FieldTypes\Traits\IsFillable;
use JCSoriano\LaravelCrudStubs\FieldTypes\Traits\IsSimpleResourceField;

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

    public function factory(): string
    {
        return 'fake()->paragraph()';
    }
}
