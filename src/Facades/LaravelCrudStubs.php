<?php

namespace JCSoriano\LaravelCrudStubs\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JCSoriano\LaravelCrudStubs\LaravelCrudStubs
 */
class LaravelCrudStubs extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \JCSoriano\LaravelCrudStubs\LaravelCrudStubs::class;
    }
}
