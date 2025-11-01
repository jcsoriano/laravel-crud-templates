<?php

namespace JCSoriano\CrudTemplates\Generators\Package;

use JCSoriano\CrudTemplates\DataObjects\Payload;
use JCSoriano\CrudTemplates\Generators\Generator;
use JCSoriano\CrudTemplates\Generators\StandardGenerator;

class TemplateGenerator extends Generator
{
    use StandardGenerator;

    protected function directory(Payload $payload): string
    {
        return app_path('Templates');
    }

    protected function fileName(Payload $payload): string
    {
        return $payload->model->model()->studlyCase();
    }

    protected function fileType(Payload $payload): string
    {
        return 'Template';
    }

    protected function stubPath(Payload $payload): string
    {
        return 'crud-templates/template.stub';
    }

    protected function variables(Payload $payload): array
    {
        return [
            'namespace' => 'App\\Templates',
            'class' => $payload->model->model()->studlyCase(),
        ];
    }
}
