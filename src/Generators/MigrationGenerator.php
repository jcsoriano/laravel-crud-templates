<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

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
        if ($this->checkIfFileExists('Migration', $directory, $fileName, $payload)) {
            return $payload;
        }

        $output = $this->print('migrations', $payload);

        $this->generateFile(
            stubPath: 'api.migration.stub',
            directory: $directory,
            fileName: $fileName,
            variables: [
                ...$payload->variables(),
                'MIGRATION_FIELDS' => $output->output,
            ],
            conditions: $payload->conditions(),
        );

        $this->logGeneratedFile('Migration', $directory, $fileName, $payload);

        return $payload;
    }
}
