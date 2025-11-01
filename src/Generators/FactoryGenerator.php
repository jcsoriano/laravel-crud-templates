<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

class FactoryGenerator extends Generator
{
    use StandardGenerator;

    protected function directory(Payload $payload): string
    {
        $namespace = $payload->model->namespace();

        return database_path('factories/'.str_replace('\\', '/', $namespace));
    }

    protected function fileName(Payload $payload): string
    {
        return $payload->model->model()->studlyCase().'Factory';
    }

    protected function fileType(Payload $payload): string
    {
        return 'Factory';
    }

    protected function stubPath(Payload $payload): string
    {
        return 'api.factory.stub';
    }

    protected function variables(Payload $payload): array
    {
        $modelName = $payload->model->model()->studlyCase();

        $output = $this->print('factory', $payload);

        // Build proper namespace paths
        $modelNamespace = $this->buildNamespace('App\\Models', $payload);

        // Collect namespaces from field types
        $namespaces = collect([
            "{$modelNamespace}\\{$modelName}",
            'Illuminate\Database\Eloquent\Factories\Factory',
        ])->merge($output->namespaces);

        return [
            ...$payload->variables(),
            'FACTORY_FIELDS' => $output->output,
            'NAMESPACES' => $this->buildNamespaces($namespaces),
        ];
    }
}
