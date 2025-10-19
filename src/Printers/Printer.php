<?php

namespace JCSoriano\LaravelCrudStubs\Printers;

use JCSoriano\LaravelCrudStubs\DataObjects\Output;
use JCSoriano\LaravelCrudStubs\DataObjects\Payload;

interface Printer
{
    public function print(Payload $payload): Output;
}
