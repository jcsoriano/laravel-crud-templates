<?php

namespace JCSoriano\LaravelCrudStubs\Pipelines;

class ApiPipeline extends Pipeline
{
    public function pipeline(): array
    {
        return $this->buildGenerators([
            'controller',
            'model',
            'policy',
            'store-request',
            'update-request',
            'resource',
            'migration',
            'factory',
            'test',
            'api-route',
            'pint',
        ]);
    }
}
