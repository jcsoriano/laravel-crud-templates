<?php

namespace JCSoriano\CrudTemplates\Generators;

use JCSoriano\CrudTemplates\DataObjects\Field;
use JCSoriano\CrudTemplates\DataObjects\Payload;

class ControllerGenerator extends Generator
{
    use StandardGenerator;

    protected function directory(Payload $payload): string
    {
        $namespace = $payload->model->namespace();

        return app_path('Http/Controllers/Api/'.str_replace('\\', '/', $namespace));
    }

    protected function fileName(Payload $payload): string
    {
        return $payload->model->model()->studlyCase().'Controller';
    }

    protected function fileType(Payload $payload): string
    {
        return 'Controller';
    }

    protected function stubPath(Payload $payload): string
    {
        return 'api.controller.stub';
    }

    protected function variables(Payload $payload): array
    {
        $model = $payload->model;
        $modelName = $model->model()->studlyCase();

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

        return [
            ...$payload->variables(),
            'RELATIONS_LIST' => $relationsList,
            'NAMESPACES' => $this->buildNamespaces($namespaces),
        ];
    }
}
