<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;
use JCSoriano\LaravelCrudTemplates\Facades\LaravelStub;

class PolicyGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $modelName = $model->model()->studlyCase();

        $directory = app_path('Policies');
        $this->createDirectoryIfNotExists($directory);

        $fileName = $modelName.'Policy';

        // Check if file exists and return early if not forcing
        if ($this->logIfFileExists('Policy', $directory, $fileName, $payload)) {
            return $payload;
        }

        // Build proper namespace paths
        $modelNamespace = $this->buildNamespace('App\\Models', $payload);

        $namespaces = collect([
            "{$modelNamespace}\\{$modelName}",
            'App\Models\User',
            'Illuminate\Auth\Access\Response',
        ]);

        LaravelStub::from($this->getStubPath('api.policy.stub'))
            ->to($directory)
            ->name($fileName)
            ->ext('php')
            ->replaces([
                ...$payload->variables(),
                'NAMESPACES' => $this->buildNamespaces($namespaces),
            ])
            ->conditions($payload->conditions())
            ->generate();

        $this->logGeneratedFile('Policy', $directory, $fileName, $payload);

        return $payload;
    }
}
