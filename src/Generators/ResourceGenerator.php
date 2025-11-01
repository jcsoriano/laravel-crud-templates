<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

class ResourceGenerator extends Generator
{
    use StandardGenerator;

    protected function directory(Payload $payload): string
    {
        $namespace = $payload->model->namespace();

        return app_path('Http/Resources/'.str_replace('\\', '/', $namespace));
    }

    protected function fileName(Payload $payload): string
    {
        return $payload->model->model()->studlyCase().'Resource';
    }

    protected function fileType(Payload $payload): string
    {
        return 'Resource';
    }

    protected function stubPath(Payload $payload): string
    {
        return 'api.resource.stub';
    }

    protected function variables(Payload $payload): array
    {
        $resourceOnlyOutput = $this->print('resource-only', $payload);
        $resourceRelationsOutput = $this->print('resource-relation', $payload);

        // Collect namespaces from printers
        $namespaces = collect([
            'Illuminate\Http\Request',
            'Illuminate\Http\Resources\Json\JsonResource',
        ])->merge($resourceRelationsOutput->namespaces);

        return [
            ...$payload->variables(),
            'RESOURCE_ONLY' => $resourceOnlyOutput->output,
            'RESOURCE_RELATIONS' => $resourceRelationsOutput->output,
            'NAMESPACES' => $this->buildNamespaces($namespaces),
        ];
    }
}
