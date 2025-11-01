<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

class MigrationGenerator extends Generator
{
    use StandardGenerator;

    protected function shouldSkipGeneration(Payload $payload): bool
    {
        if ($payload->table) {
            $payload->components->warn('--table used so the table already exists. Skipping migration generation');

            return true;
        }

        return false;
    }

    protected function directory(Payload $payload): string
    {
        return database_path('migrations');
    }

    protected function fileName(Payload $payload): string
    {
        $timestamp = now()->format('Y_m_d_His');
        $tableName = $payload->model->model()->pluralSnakeCase();

        return $timestamp.'_create_'.$tableName.'_table';
    }

    protected function fileType(Payload $payload): string
    {
        return 'Migration';
    }

    protected function stubPath(Payload $payload): string
    {
        return 'api.migration.stub';
    }

    protected function variables(Payload $payload): array
    {
        $output = $this->print('migrations', $payload);

        return [
            ...$payload->variables(),
            'MIGRATION_FIELDS' => $output->output,
        ];
    }
}
