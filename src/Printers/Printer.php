<?php

namespace JCSoriano\LaravelCrudStubs\Printers;

use Illuminate\Support\Collection;
use JCSoriano\LaravelCrudStubs\DataObjects\Output;

interface Printer
{
    public function print(Collection $fields): Output;
}
