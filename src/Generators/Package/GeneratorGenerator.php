<?php

namespace JCSoriano\CrudTemplates\Generators\Package;

use JCSoriano\CrudTemplates\DataObjects\Payload;
use JCSoriano\CrudTemplates\Generators\Generator;
use JCSoriano\CrudTemplates\Generators\StandardGenerator;

class GeneratorGenerator extends Generator
{
    use StandardGenerator;

    protected function directory(Payload $payload): string
    {
        return app_path('Generators');
    }

    protected function fileName(Payload $payload): string
    {
        return $payload->model->model()->studlyCase().'Generator';
    }

    protected function fileType(Payload $payload): string
    {
        return 'Generator';
    }

    protected function stubPath(Payload $payload): string
    {
        return 'crud-templates/'.$payload->options['stub'];
    }

    protected function variables(Payload $payload): array
    {
        return [
            'namespace' => 'App\\Generators',
            'class' => $this->fileName($payload),
        ];
    }
}
