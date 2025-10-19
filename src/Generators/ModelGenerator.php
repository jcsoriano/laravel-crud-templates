<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;
use JCSoriano\LaravelCrudTemplates\Facades\LaravelStub;

class ModelGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $namespace = $model->namespace();
        $modelName = $model->model()->studlyCase();

        $directory = app_path('Models/'.str_replace('\\', '/', $namespace));
        $this->createDirectoryIfNotExists($directory);

        $fileName = $modelName;

        // Check if file exists and return early if not forcing
        if ($this->logIfFileExists('Model', $directory, $fileName, $payload)) {
            return $payload;
        }

        $fillableOutput = $this->print('fillable', $payload);
        $castsOutput = $this->print('casts', $payload);
        $relationsOutput = $this->print('relations', $payload);

        // Collect namespaces from printers
        $namespaces = collect([
            'Illuminate\Database\Eloquent\Factories\HasFactory',
            'Illuminate\Database\Eloquent\Model',
            'Illuminate\Database\Eloquent\SoftDeletes',
        ])
            ->merge($castsOutput->namespaces)
            ->merge($relationsOutput->namespaces);

        LaravelStub::from($this->getStubPath('api.model.stub'))
            ->to($directory)
            ->name($fileName)
            ->ext('php')
            ->replaces([
                ...$payload->variables(),
                'FILLABLE' => $fillableOutput->output,
                'CASTS' => $castsOutput->output,
                'RELATIONS' => $relationsOutput->output,
                'NAMESPACES' => $this->buildNamespaces($namespaces),
            ])
            ->conditions($payload->conditions())
            ->generate();

        $this->logGeneratedFile('Model', $directory, $fileName, $payload);

        return $payload;
    }
}
