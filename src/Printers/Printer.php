<?php

namespace JCSoriano\CrudTemplates\Printers;

use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\DataObjects\Payload;

interface Printer
{
    public function print(Payload $payload): Output;
}
