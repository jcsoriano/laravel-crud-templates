<?php

namespace JCSoriano\CrudTemplates\Generators\Package;

use JCSoriano\CrudTemplates\DataObjects\Payload;
use JCSoriano\CrudTemplates\Generators\Generator;
use JCSoriano\CrudTemplates\Generators\StandardGenerator;

class FieldTypeGenerator extends Generator
{
    use StandardGenerator;

    protected function directory(Payload $payload): string
    {
        return app_path('FieldTypes');
    }

    protected function fileName(Payload $payload): string
    {
        return $payload->model->model()->studlyCase();
    }

    protected function fileType(Payload $payload): string
    {
        return 'Field Type';
    }

    protected function stubPath(Payload $payload): string
    {
        return 'crud-templates/field-type.stub';
    }

    protected function variables(Payload $payload): array
    {
        return [
            'namespace' => 'App\\FieldTypes',
            'class' => $payload->model->model()->studlyCase(),
        ];
    }
}
