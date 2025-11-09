<?php

namespace JCSoriano\CrudTemplates\Generators;

use Illuminate\Support\Facades\Schema;
use JCSoriano\CrudTemplates\DataObjects\Payload;

class MigrationGenerator extends Generator
{
    use StandardGenerator;

    protected function shouldSkipGeneration(Payload $payload): bool
    {
        if ($payload->table) {
            $payload->components->warn('--table used so the table already exists. Skipping migration generation');

            return true;
        }

        // check if the table already exists in the database
        $tableName = $payload->model->model()->pluralSnakeCase();
        if (Schema::hasTable($tableName)) {
            $payload->components->warn('Table already exists. Skipping migration generation');

            return true;
        }

        $migrations = File::allFiles($this->directory($payload));
        $migration = $migrations->first(
            fn ($migration) => Str::endsWith($migration->getFilename(), $this->migrationName($payload).'.php')
        );
        if ($migration) {
            $payload->components->warn('Migration already exists. Skipping migration generation');

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

        return $timestamp.$this->migrationName($payload);
    }

    protected function migrationName(Payload $payload): string
    {
        $tableName = $payload->model->model()->pluralSnakeCase();

        return 'create_'.$tableName.'_table';
    }

    protected function fileType(Payload $payload): string
    {
        return 'Migration';
    }

    protected function stubPath(Payload $payload): string
    {
        return 'api/migration.stub';
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
