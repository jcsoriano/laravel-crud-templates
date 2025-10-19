<?php

namespace JCSoriano\LaravelCrudTemplates\Generators;

use JCSoriano\LaravelCrudTemplates\Facades\LaravelStub;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;
use JCSoriano\LaravelCrudTemplates\LaravelCrudTemplates;

class MigrationGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $tableName = $model->model()->pluralSnakeCase();

        $directory = database_path('migrations');
        $this->createDirectoryIfNotExists($directory);

        $timestamp = now()->format('Y_m_d_His');
        $fileName = $timestamp.'_create_'.$tableName.'_table';

        $migrationsPrinter = LaravelCrudTemplates::buildPrinter('migrations');
        $output = $migrationsPrinter->print($payload);

        LaravelStub::from($this->getStubPath('crud.migration.stub'))
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
