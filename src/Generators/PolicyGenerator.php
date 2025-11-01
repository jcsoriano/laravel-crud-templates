<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

class PolicyGenerator extends Generator
{
    use StandardGenerator;

    protected function directory(Payload $payload): string
    {
        return app_path('Policies');
    }

    protected function fileName(Payload $payload): string
    {
        return $payload->model->model()->studlyCase().'Policy';
    }

    protected function fileType(Payload $payload): string
    {
        return 'Policy';
    }

    protected function stubPath(Payload $payload): string
    {
        return 'api.policy.stub';
    }

    protected function variables(Payload $payload): array
    {
        $modelName = $payload->model->model()->studlyCase();

        // Build proper namespace paths
        $modelNamespace = $this->buildNamespace('App\\Models', $payload);

        $namespaces = collect([
            "{$modelNamespace}\\{$modelName}",
            'App\Models\User',
            'Illuminate\Auth\Access\Response',
        ]);

        return [
            ...$payload->variables(),
            'NAMESPACES' => $this->buildNamespaces($namespaces),
        ];
    }
}
