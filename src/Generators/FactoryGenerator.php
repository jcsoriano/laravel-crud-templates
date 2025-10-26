<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

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

        // Check if file exists and return early if not forcing
        if ($this->checkIfFileExists('Factory', $directory, $fileName, $payload)) {
            return $payload;
        }

        $output = $this->print('factory', $payload);

        // Build proper namespace paths
        $modelNamespace = $this->buildNamespace('App\\Models', $payload);

        // Collect namespaces from field types
        $namespaces = collect([
            "{$modelNamespace}\\{$modelName}",
            'Illuminate\Database\Eloquent\Factories\Factory',
        ])->merge($output->namespaces);

        $this->generateFile(
            stubPath: 'api.factory.stub',
            directory: $directory,
            fileName: $fileName,
            variables: [
                ...$payload->variables(),
                'FACTORY_FIELDS' => $output->output,
                'NAMESPACES' => $this->buildNamespaces($namespaces),
            ],
            conditions: $payload->conditions(),
        );

        $this->logGeneratedFile('Factory', $directory, $fileName, $payload);

        return $payload;
    }
}
