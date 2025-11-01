<?php

namespace JCSoriano\CrudTemplates\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JCSoriano\CrudTemplates\LaravelStub
 */
class LaravelStub extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \JCSoriano\CrudTemplates\LaravelStub::class;
    }
}
