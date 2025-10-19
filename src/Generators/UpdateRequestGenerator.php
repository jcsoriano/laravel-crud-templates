<?php

namespace JCSoriano\LaravelCrudStubs\Generators;

use Binafy\LaravelStub\Facades\LaravelStub;
use JCSoriano\LaravelCrudStubs\DataObjects\Payload;
use JCSoriano\LaravelCrudStubs\LaravelCrudStubs;

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

        $rulesPrinter = LaravelCrudStubs::buildPrinter('rules');
        $rulesOutput = $rulesPrinter->print($payload);

        // Collect namespaces from printer
        $namespaces = collect([
            'Illuminate\Foundation\Http\FormRequest',
        ])->merge($rulesOutput->namespaces);

        LaravelStub::from($this->getStubPath('crud.request.update.stub'))
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

        $this->printSuccess('Request', $directory, $fileName, $payload);

        return $payload;
    }
}
