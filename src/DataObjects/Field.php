<?php

namespace JCSoriano\LaravelCrudStubs\DataObjects;

class Field
{
    /**
     * @param  class-string  $typeClass
     */
    public function __construct(
        public protected(set) Name $name,
        public protected(set) bool $required,
        public protected(set) string $typeClass,
        public protected(set) array $options = [],
    ) {}
}
