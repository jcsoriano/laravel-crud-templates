<?php

namespace JCSoriano\CrudTemplates\Generators;

use Illuminate\Support\Facades\Artisan;
use JCSoriano\CrudTemplates\DataObjects\Payload;

class DependenciesGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $relatedModels = $this->collectRelatedModels($payload);

        foreach ($relatedModels as $modelClass => $modelInfo) {
            // Check and create model if it doesn't exist
            if (! class_exists($modelClass)) {
                $this->createModel($modelInfo, $payload);
            }

            // Check and create resource if it doesn't exist
            $resourceClass = $this->getResourceClass($modelInfo);
            if (! class_exists($resourceClass)) {
                $this->createResource($modelInfo, $payload);
            }
        }

        return $payload;
    }

    /**
     * Collect all unique related models from fields.
     *
     * @return array<string, array{namespace: string, modelName: string, modelClass: string}>
     */
    protected function collectRelatedModels(Payload $payload): array
    {
        $relatedModels = [];

        foreach ($payload->fields as $field) {
            // Skip fields without related models
            if (! $this->hasRelatedModel($field)) {
                continue;
            }

            $modelClass = $field->relatedClass('App\\Models');
            $modelInfo = $this->parseModelClass($modelClass);

            // Store unique models by class name
            if (! isset($relatedModels[$modelClass])) {
                $relatedModels[$modelClass] = $modelInfo;
            }
        }

        return $relatedModels;
    }

    /**
     * Check if a field has a related model.
     */
    protected function hasRelatedModel($field): bool
    {
        // Field has explicit model specified
        if ($field->model !== null) {
            return true;
        }

        // Check if field type uses ParsesRelatedModel trait
        $fieldTypeClass = $field->typeClass;
        $usesParsesRelatedModel = in_array(
            'JCSoriano\\CrudTemplates\\FieldTypes\\Traits\\ParsesRelatedModel',
            class_uses_recursive($fieldTypeClass)
        );

        return $usesParsesRelatedModel;
    }

    /**
     * Parse model class into namespace and model name.
     *
     * @return array{namespace: string, modelName: string, modelClass: string}
     */
    protected function parseModelClass(string $modelClass): array
    {
        // Remove 'App\Models\' prefix
        $relativePath = str_replace('App\\Models\\', '', $modelClass);

        // Split into namespace parts and model name
        $parts = explode('\\', $relativePath);
        $modelName = array_pop($parts);
        $namespace = implode('\\', $parts);

        return [
            'namespace' => $namespace,
            'modelName' => $modelName,
            'modelClass' => $modelClass,
        ];
    }

    /**
     * Get the resource class name for a model.
     */
    protected function getResourceClass(array $modelInfo): string
    {
        $namespace = $modelInfo['namespace'];
        $modelName = $modelInfo['modelName'];

        $resourceNamespace = collect(['App', 'Http', 'Resources'])
            ->merge($namespace ? explode('\\', $namespace) : [])
            ->filter()
            ->join('\\');

        return $resourceNamespace.'\\'.$modelName.'Resource';
    }

    /**
     * Create a model using artisan command.
     */
    protected function createModel(array $modelInfo, Payload $payload): void
    {
        $namespace = $modelInfo['namespace'];
        $modelName = $modelInfo['modelName'];

        // Convert namespace from \ to / for artisan command
        $namespacePath = $namespace ? str_replace('\\', '/', $namespace) : '';
        $modelPath = $namespacePath ? "{$namespacePath}/{$modelName}" : $modelName;

        $command = "make:model {$modelPath} --migration";
        $exitCode = Artisan::call($command);

        $payload->components->info("Model [{$modelInfo['modelClass']}] created successfully.");
    }

    /**
     * Create a resource using artisan command.
     */
    protected function createResource(array $modelInfo, Payload $payload): void
    {
        $namespace = $modelInfo['namespace'];
        $modelName = $modelInfo['modelName'];

        // Convert namespace from \ to / for artisan command
        $namespacePath = $namespace ? str_replace('\\', '/', $namespace) : '';
        $resourcePath = $namespacePath ? "{$namespacePath}/{$modelName}Resource" : "{$modelName}Resource";

        $resourceClass = $this->getResourceClass($modelInfo);

        $command = "make:resource {$resourcePath}";
        $exitCode = Artisan::call($command);

        $payload->components->info("Resource [{$resourceClass}] created successfully.");
    }
}
