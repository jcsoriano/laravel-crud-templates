<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\Facades\LaravelStub;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;
use JCSoriano\LaravelCrudTemplates\LaravelCrudTemplates;

class StoreRequestGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $namespace = $model->namespace();
        $modelName = $model->model()->studlyCase();

        $directory = app_path('Http/Requests/'.str_replace('\\', '/', $namespace));
        $this->createDirectoryIfNotExists($directory);

        $fileName = 'Store'.$modelName.'Request';

        $rulesPrinter = LaravelCrudTemplates::buildPrinter('rules');
        $rulesOutput = $rulesPrinter->print($payload);

        // Collect namespaces from printer
        $namespaces = collect([
            'Illuminate\Foundation\Http\FormRequest',
        ])->merge($rulesOutput->namespaces);

        LaravelStub::from($this->getStubPath('crud.request.store.stub'))
            ->to($directory)
            ->name($fileName)
            ->ext('php')
            ->replaces([
                ...$payload->variables(),
                'RULES' => $rulesOutput->output,
                'NAMESPACES' => $this->buildNamespaces($namespaces),
            ])
            ->conditions($payload->conditions())
            ->generate();

        $this->logGeneratedFile('Request', $directory, $fileName, $payload);

        return $payload;
    }
}
