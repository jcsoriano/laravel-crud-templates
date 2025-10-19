<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use Binafy\LaravelStub\Facades\LaravelStub;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

class ControllerGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $namespace = $model->namespace();
        $modelName = $model->model()->studlyCase();

        $directory = app_path('Http/Controllers/Api/'.str_replace('\\', '/', $namespace));
        $this->createDirectoryIfNotExists($directory);

        $fileName = $modelName.'Controller';

        // Build proper namespace paths
        $requestNamespace = $this->buildNamespace('App\\Http\\Requests', $payload);
        $resourceNamespace = $this->buildNamespace('App\\Http\\Resources', $payload);
        $modelNamespace = $this->buildNamespace('App\\Models', $payload);

        $namespaces = collect([
            'App\Http\Controllers\Controller',
            "{$requestNamespace}\\Store{$modelName}Request",
            "{$requestNamespace}\\Update{$modelName}Request",
            "{$resourceNamespace}\\{$modelName}Resource",
            "{$modelNamespace}\\{$modelName}",
            'Illuminate\Http\Request',
            'Illuminate\Support\Facades\Gate',
        ]);

        $scope = $payload->options['scope'] ?? null;
        if (in_array($scope, ['user', 'team'])) {
            $namespaces->push('Illuminate\Support\Facades\Auth');
        }

        LaravelStub::from($this->getStubPath('crud.controller.stub'))
            ->to($directory)
            ->name($fileName)
            ->ext('php')
            ->replaces([
                ...$payload->variables(),
                'NAMESPACES' => $this->buildNamespaces($namespaces),
            ])
            ->conditions($payload->conditions())
            ->generate();

        $this->printSuccess('Controller', $directory, $fileName, $payload);

        return $payload;
    }
}
