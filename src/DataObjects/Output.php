<?php

namespace JCSoriano\LaravelCrudTemplates\DataObjects;

use Illuminate\Support\Collection;

class Output
{
    public function __construct(
        public protected(set) string $output,
        public protected(set) ?Collection $namespaces = null,
    ) {}
}
