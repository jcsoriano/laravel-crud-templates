<?php

namespace JCSoriano\LaravelCrudStubs\Generators;

use Binafy\LaravelStub\Facades\LaravelStub;
use JCSoriano\LaravelCrudStubs\DataObjects\Payload;

class PolicyGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $modelName = $model->model()->studlyCase();

        $directory = app_path('Policies');
        $this->createDirectoryIfNotExists($directory);

        $fileName = $modelName.'Policy';

        // Build proper namespace paths
        $modelNamespace = $this->buildNamespace('App\\Models', $payload);

        $namespaces = collect([
            "{$modelNamespace}\\{$modelName}",
            'App\Models\User',
            'Illuminate\Auth\Access\Response',
        ]);

        LaravelStub::from($this->getStubPath('crud.policy.stub'))
            ->to($directory)
            ->name($fileName)
            ->ext('php')
            ->replaces([
                ...$payload->variables(),
                'NAMESPACES' => $this->buildNamespaces($namespaces),
            ])
            ->conditions($payload->conditions())
            ->generate();

        $this->printSuccess('Policy', $directory, $fileName, $payload);

        return $payload;
    }
}
