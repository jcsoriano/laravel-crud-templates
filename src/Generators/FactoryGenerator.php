<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use Binafy\LaravelStub\Facades\LaravelStub;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;
use JCSoriano\LaravelCrudTemplates\LaravelCrudTemplates;

class FactoryGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $namespace = $model->namespace();
        $modelName = $model->model()->studlyCase();

        $directory = database_path('factories/'.str_replace('\\', '/', $namespace));
        $this->createDirectoryIfNotExists($directory);

        $fileName = $modelName.'Factory';

        $factoryPrinter = LaravelCrudTemplates::buildPrinter('factory');
        $output = $factoryPrinter->print($payload);

        // Build proper namespace paths
        $modelNamespace = $this->buildNamespace('App\\Models', $payload);

        // Collect namespaces from field types
        $namespaces = collect([
            "{$modelNamespace}\\{$modelName}",
            'Illuminate\Database\Eloquent\Factories\Factory',
        ])->merge($output->namespaces);

        LaravelStub::from($this->getStubPath('crud.factory.stub'))
            ->to($directory)
            ->name($fileName)
            ->ext('php')
            ->replaces([
                ...$payload->variables(),
                'FACTORY_FIELDS' => $output->output,
                'NAMESPACES' => $this->buildNamespaces($namespaces),
            ])
            ->conditions($payload->conditions())
            ->generate();

        $this->printSuccess('Factory', $directory, $fileName, $payload);

        return $payload;
    }
}
