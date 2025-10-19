<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;
use JCSoriano\LaravelCrudTemplates\Facades\LaravelStub;

class MigrationGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        // Skip migration generation if --table is used
        if ($payload->table) {
            $payload->components->warn('--table used so the table already exists. Skipping migration generation');

            return $payload;
        }

        $model = $payload->model;
        $tableName = $model->model()->pluralSnakeCase();

        $directory = database_path('migrations');
        $this->createDirectoryIfNotExists($directory);

        $timestamp = now()->format('Y_m_d_His');
        $fileName = $timestamp.'_create_'.$tableName.'_table';

        // Check if file exists and return early if not forcing
        if ($this->logIfFileExists('Migration', $directory, $fileName, $payload)) {
            return $payload;
        }

        $output = $this->print('migrations', $payload);

        LaravelStub::from($this->getStubPath('api.migration.stub'))
            ->to($directory)
            ->name($fileName)
            ->ext('php')
            ->replaces([
                ...$payload->variables(),
                'MIGRATION_FIELDS' => $output->output,
            ])
            ->conditions($payload->conditions())
            ->generate();

        $this->logGeneratedFile('Migration', $directory, $fileName, $payload);

        return $payload;
    }
}
