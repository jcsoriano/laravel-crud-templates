<?php

namespace JCSoriano\CrudTemplates\Generators;

use JCSoriano\CrudTemplates\DataObjects\Payload;

class StoreRequestGenerator extends Generator
{
    use StandardGenerator;

    protected function directory(Payload $payload): string
    {
        $namespace = $payload->model->namespace();

        return app_path('Http/Requests/'.str_replace('\\', '/', $namespace));
    }

    protected function fileName(Payload $payload): string
    {
        return 'Store'.$payload->model->model()->studlyCase().'Request';
    }

    protected function fileType(Payload $payload): string
    {
        return 'Request';
    }

    protected function stubPath(Payload $payload): string
    {
        return 'api.request.store.stub';
    }

    protected function variables(Payload $payload): array
    {
        $rulesOutput = $this->print('rules', $payload);

        // Collect namespaces from printer
        $namespaces = collect([
            'Illuminate\Foundation\Http\FormRequest',
        ])->merge($rulesOutput->namespaces);

        return [
            ...$payload->variables(),
            'RULES' => $rulesOutput->output,
            'NAMESPACES' => $this->buildNamespaces($namespaces),
        ];
    }
}
