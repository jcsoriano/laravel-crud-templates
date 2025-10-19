<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;
use JCSoriano\LaravelCrudTemplates\Facades\LaravelStub;

class ResourceGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $namespace = $model->namespace();
        $modelName = $model->model()->studlyCase();

        $directory = app_path('Http/Resources/'.str_replace('\\', '/', $namespace));
        $this->createDirectoryIfNotExists($directory);

        $fileName = $modelName.'Resource';

        // Check if file exists and return early if not forcing
        if ($this->logIfFileExists('Resource', $directory, $fileName, $payload)) {
            return $payload;
        }

        $resourceOnlyOutput = $this->print('resource-only', $payload);
        $resourceRelationsOutput = $this->print('resource-relation', $payload);

        // Collect namespaces from printers
        $namespaces = collect([
            'Illuminate\Http\Request',
            'Illuminate\Http\Resources\Json\JsonResource',
        ])->merge($resourceRelationsOutput->namespaces);

        LaravelStub::from($this->getStubPath('api.resource.stub'))
            ->to($directory)
            ->name($fileName)
            ->ext('php')
            ->replaces([
                ...$payload->variables(),
                'RESOURCE_ONLY' => $resourceOnlyOutput->output,
                'RESOURCE_RELATIONS' => $resourceRelationsOutput->output,
                'NAMESPACES' => $this->buildNamespaces($namespaces),
            ])
            ->conditions($payload->conditions())
            ->generate();

        $this->logGeneratedFile('Resource', $directory, $fileName, $payload);

        return $payload;
    }
}
