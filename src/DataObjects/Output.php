<?php

namespace JCSoriano\CrudTemplates\DataObjects;

use Illuminate\Support\Collection;

class Output
{
    public function __construct(
        public protected(set) string $output,
        public protected(set) ?Collection $namespaces = null,
    ) {}

    public function mergeNamespaces(Collection $namespaces): Collection
    {
        return $namespaces->merge($this->namespaces ?? []);
    }
}
