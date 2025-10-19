<?php

namespace JCSoriano\LaravelCrudTemplates\DataObjects;

class Model
{
    public function __construct(
        public protected(set) string $path,
    ) {}

    public function namespace(): string
    {
        $segments = explode('/', $this->path);

        if (count($segments) === 1) {
            return '';
        }

        return implode('\\', array_slice($segments, 0, -1));
    }

    public function model(): Name
    {
        $segments = explode('/', $this->path);

        return new Name(end($segments));
    }
}
