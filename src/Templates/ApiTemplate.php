<?php

namespace JCSoriano\CrudTemplates\Templates;

class ApiTemplate extends Template
{
    public function template(): array
    {
        return $this->buildGenerators([
            'controller',
            'model',
            'policy',
            'store-request',
            'update-request',
            'resource',
            'migration',
            'pivot-migration',
            'factory',
            'test',
            'api-route',
            'pint',
        ]);
    }
}
