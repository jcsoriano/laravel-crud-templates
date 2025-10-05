<?php

namespace JCSoriano\LaravelCrudStubs\Generators;

use Binafy\LaravelStub\Facades\LaravelStub;
use JCSoriano\LaravelCrudStubs\DataObjects\Payload;
use JCSoriano\LaravelCrudStubs\LaravelCrudStubs;

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

        $fillablePrinter = LaravelCrudStubs::buildPrinter('fillable');
        $castsPrinter = LaravelCrudStubs::buildPrinter('casts');
        $relationsPrinter = LaravelCrudStubs::buildPrinter('relations');

        $fillableOutput = $fillablePrinter->print($payload->fields);
        $castsOutput = $castsPrinter->print($payload->fields);
        $relationsOutput = $relationsPrinter->print($payload->fields);

        // Collect namespaces from printers
        $namespaces = collect([
            'Illuminate\Database\Eloquent\Factories\HasFactory',
            'Illuminate\Database\Eloquent\Model',
            'Illuminate\Database\Eloquent\SoftDeletes',
        ])
            ->merge($castsOutput->namespaces)
            ->merge($relationsOutput->namespaces);

        LaravelStub::from($this->getStubPath('crud.model.stub'))
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
            ->conditions($payload->conditions)
            ->generate();

        $this->printSuccess('Model', $directory, $fileName, $payload);

        return $payload;
    }
}
