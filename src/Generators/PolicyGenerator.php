<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

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
        if ($this->checkIfFileExists('Policy', $directory, $fileName, $payload)) {
            return $payload;
        }

        // Build proper namespace paths
        $modelNamespace = $this->buildNamespace('App\\Models', $payload);

        $namespaces = collect([
            "{$modelNamespace}\\{$modelName}",
            'App\Models\User',
            'Illuminate\Auth\Access\Response',
        ]);

        $this->generateFile(
            stubPath: 'api.policy.stub',
            directory: $directory,
            fileName: $fileName,
            variables: [
                ...$payload->variables(),
                'NAMESPACES' => $this->buildNamespaces($namespaces),
            ],
            conditions: $payload->conditions(),
        );

        $this->logGeneratedFile('Policy', $directory, $fileName, $payload);

        return $payload;
    }
}
