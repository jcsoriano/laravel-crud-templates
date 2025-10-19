<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\Facades\LaravelStub;
use JCSoriano\LaravelCrudTemplates\DataObjects\Field;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

class TestGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $namespace = $model->namespace();
        $modelName = $model->model()->studlyCase();

        $directory = base_path('tests/Feature/Api/'.str_replace('\\', '/', $namespace));
        $this->createDirectoryIfNotExists($directory);

        $fileName = $modelName.'ControllerTest';

        // Build test structure from fillable fields
        $testStructure = collect(['id']);

        /** @var Field $field */
        foreach ($payload->fields as $field) {
            $fieldType = new $field->typeClass($field);

            if (method_exists($fieldType, 'fillable')) {
                $testStructure->push($field->name->snakeCase());
            }
        }

        $testStructure->push('created_at', 'updated_at');
        $testStructureString = $testStructure->map(fn ($field) => "'{$field}'")->join(",\n                ");

        // Build proper namespace paths
        $modelNamespace = $this->buildNamespace('App\\Models', $payload);

        $namespaces = collect([
            "{$modelNamespace}\\{$modelName}",
            'App\Models\User',
            'Illuminate\Foundation\Testing\RefreshDatabase',
            'Illuminate\Testing\Fluent\AssertableJson',
            'Tests\TestCase',
        ]);

        LaravelStub::from($this->getStubPath('crud.test.stub'))
            ->to($directory)
            ->name($fileName)
            ->ext('php')
            ->replaces([
                ...$payload->variables(),
                'TEST_STRUCTURE' => $testStructureString,
                'NAMESPACES' => $this->buildNamespaces($namespaces),
            ])
            ->conditions($payload->conditions())
            ->generate();

        $this->logGeneratedFile('Test', $directory, $fileName, $payload);

        return $payload;
    }
}
