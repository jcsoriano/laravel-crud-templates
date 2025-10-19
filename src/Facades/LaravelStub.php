<?php

namespace JCSoriano\LaravelCrudTemplates\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JCSoriano\LaravelCrudTemplates\LaravelStub
 */
class LaravelStub extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \JCSoriano\LaravelCrudTemplates\LaravelStub::class;
    }
}
