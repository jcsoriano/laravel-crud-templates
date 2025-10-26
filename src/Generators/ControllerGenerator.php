<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\DataObjects\Field;
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

        // Check if file exists and return early if not forcing
        if ($this->checkIfFileExists('Controller', $directory, $fileName, $payload)) {
            return $payload;
        }

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

        $relationsList = $payload->fields->filter(
            fn (Field $field): bool => method_exists($field->typeClass, 'relation')
        )->map(
            fn (Field $field): string => "'{$field->name->camelCase()}'",
        )->join(",\n            ");

        $scope = $payload->options['scope'] ?? null;
        if (in_array($scope, ['user', 'team'])) {
            $namespaces->push('Illuminate\Support\Facades\Auth');
        }

        $this->generateFile(
            stubPath: 'api.controller.stub',
            directory: $directory,
            fileName: $fileName,
            variables: [
                ...$payload->variables(),
                'RELATIONS_LIST' => $relationsList,
                'NAMESPACES' => $this->buildNamespaces($namespaces),
            ],
            conditions: $payload->conditions(),
        );

        $this->logGeneratedFile('Controller', $directory, $fileName, $payload);

        return $payload;
    }
}
