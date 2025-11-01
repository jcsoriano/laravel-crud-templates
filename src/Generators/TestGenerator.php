<?php

namespace JCSoriano\CrudTemplates\Generators;

use JCSoriano\CrudTemplates\DataObjects\Field;
use JCSoriano\CrudTemplates\DataObjects\Payload;

class TestGenerator extends Generator
{
    use StandardGenerator;

    protected function directory(Payload $payload): string
    {
        $namespace = $payload->model->namespace();

        return base_path('tests/Feature/Api/'.str_replace('\\', '/', $namespace));
    }

    protected function fileName(Payload $payload): string
    {
        return $payload->model->model()->studlyCase().'ControllerTest';
    }

    protected function fileType(Payload $payload): string
    {
        return 'Test';
    }

    protected function stubPath(Payload $payload): string
    {
        return 'api.test.stub';
    }

    protected function variables(Payload $payload): array
    {
        $modelName = $payload->model->model()->studlyCase();

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

        $dbAssertionsOutput = $this->print('dbAssertions', $payload);

        // Build proper namespace paths
        $modelNamespace = $this->buildNamespace('App\\Models', $payload);

        $namespaces = collect([
            "{$modelNamespace}\\{$modelName}",
            'App\Models\User',
            'Illuminate\Foundation\Testing\RefreshDatabase',
            'Illuminate\Testing\Fluent\AssertableJson',
            'Tests\TestCase',
        ])->merge($dbAssertionsOutput->namespaces);

        return [
            ...$payload->variables(),
            'TEST_STRUCTURE' => $testStructureString,
            'NAMESPACES' => $this->buildNamespaces($namespaces),
            'DB_ASSERTION_COLUMNS' => $dbAssertionsOutput->output,
        ];
    }
}
