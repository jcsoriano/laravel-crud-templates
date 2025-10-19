<?php

namespace JCSoriano\LaravelCrudTemplates\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JCSoriano\LaravelCrudTemplates\LaravelCrudTemplates
 */
class LaravelCrudTemplates extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \JCSoriano\LaravelCrudTemplates\LaravelCrudTemplates::class;
    }
}
