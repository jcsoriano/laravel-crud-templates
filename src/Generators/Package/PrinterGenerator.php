<?php

namespace JCSoriano\CrudTemplates\Generators\Package;

use JCSoriano\CrudTemplates\DataObjects\Payload;
use JCSoriano\CrudTemplates\Generators\Generator;
use JCSoriano\CrudTemplates\Generators\StandardGenerator;

class PrinterGenerator extends Generator
{
    use StandardGenerator;

    protected function directory(Payload $payload): string
    {
        return app_path('Printers');
    }

    protected function fileName(Payload $payload): string
    {
        return $payload->model->model()->studlyCase().'Printer';
    }

    protected function fileType(Payload $payload): string
    {
        return 'Printer';
    }

    protected function stubPath(Payload $payload): string
    {
        return 'crud-templates/printer.stub';
    }

    protected function variables(Payload $payload): array
    {
        return [
            'namespace' => 'App\\Printers',
            'class' => $this->fileName($payload),
        ];
    }
}
