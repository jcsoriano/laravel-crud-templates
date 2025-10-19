<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use Binafy\LaravelStub\Facades\LaravelStub;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;
use JCSoriano\LaravelCrudTemplates\LaravelCrudTemplates;

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

        $resourceOnlyPrinter = LaravelCrudTemplates::buildPrinter('resource-only');
        $resourceRelationPrinter = LaravelCrudTemplates::buildPrinter('resource-relation');

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
