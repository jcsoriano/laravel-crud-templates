<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

class UpdateRequestGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $namespace = $model->namespace();
        $modelName = $model->model()->studlyCase();

        $directory = app_path('Http/Requests/'.str_replace('\\', '/', $namespace));
        $this->createDirectoryIfNotExists($directory);

        $fileName = 'Update'.$modelName.'Request';

        // Check if file exists and return early if not forcing
        if ($this->checkIfFileExists('Request', $directory, $fileName, $payload)) {
            return $payload;
        }

        $rulesOutput = $this->print('rules', $payload);

        // Collect namespaces from printer
        $namespaces = collect([
            'Illuminate\Foundation\Http\FormRequest',
        ])->merge($rulesOutput->namespaces);

        $this->generateFile(
            stubPath: 'api.request.update.stub',
            directory: $directory,
            fileName: $fileName,
            variables: [
                ...$payload->variables(),
                'RULES' => $rulesOutput->output,
                'NAMESPACES' => $this->buildNamespaces($namespaces),
            ],
            conditions: $payload->conditions(),
        );

        $this->logGeneratedFile('Request', $directory, $fileName, $payload);

        return $payload;
    }
}
