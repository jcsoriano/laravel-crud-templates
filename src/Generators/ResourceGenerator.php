<?php

namespace JCSoriano\LaravelCrudStubs\Generators;

use Binafy\LaravelStub\Facades\LaravelStub;
use JCSoriano\LaravelCrudStubs\DataObjects\Payload;
use JCSoriano\LaravelCrudStubs\LaravelCrudStubs;

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

        $resourceOnlyPrinter = LaravelCrudStubs::buildPrinter('resource-only');
        $resourceRelationPrinter = LaravelCrudStubs::buildPrinter('resource-relation');

        $resourceOnlyOutput = $resourceOnlyPrinter->print($payload);
        $resourceRelationsOutput = $resourceRelationPrinter->print($payload);

        // Collect namespaces from printers
        $namespaces = collect([
            'Illuminate\Http\Request',
            'Illuminate\Http\Resources\Json\JsonResource',
        ])->merge($resourceRelationsOutput->namespaces);

        LaravelStub::from($this->getStubPath('crud.resource.stub'))
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

        $this->printSuccess('Resource', $directory, $fileName, $payload);

        return $payload;
    }
}
