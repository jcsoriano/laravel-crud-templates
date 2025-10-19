<?php

namespace JCSoriano\LaravelCrudTemplates\DataObjects;

use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class Name
{
    public protected(set) Stringable $name;

    public function __construct(
        string $name,
    ) {
        $this->name = Str::of($name);
    }

    public function snakeCase(): string
    {
        return $this->name->snake();
    }

    public function pluralSnakeCase(): string
    {
        return $this->name->plural()->snake();
    }

    public function kebabCase(): string
    {
        return $this->name->kebab();
    }

    public function pluralKebabCase(): string
    {
        return $this->name->plural()->kebab();
    }

    public function camelCase(): string
    {
        return $this->name->camel();
    }

    public function pluralCamelCase(): string
    {
        return $this->name->plural()->camel();
    }

    public function studlyCase(): string
    {
        return $this->name->studly();
    }

    public function pluralStudlyCase(): string
    {
        return $this->name->plural()->studly();
    }
}
