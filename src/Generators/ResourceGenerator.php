<?php

namespace JCSoriano\CrudTemplates\Generators;

use JCSoriano\CrudTemplates\DataObjects\Payload;

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
        return 'api/resource.stub';
    }

    protected function variables(Payload $payload): array
    {
        $resourceOutput = $this->print('resource', $payload);

        // Collect namespaces from printers
        $namespaces = collect([
            'Illuminate\Http\Request',
            'Illuminate\Http\Resources\Json\JsonResource',
        ])->merge($resourceOutput->namespaces);

        return [
            ...$payload->variables(),
            'RESOURCE_FIELDS' => $resourceOutput->output,
            'NAMESPACES' => $this->buildNamespaces($namespaces),
        ];
    }
}
