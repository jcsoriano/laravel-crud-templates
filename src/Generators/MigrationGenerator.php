<?php

namespace JCSoriano\LaravelCrudStubs\Generators;

use Binafy\LaravelStub\Facades\LaravelStub;
use JCSoriano\LaravelCrudStubs\DataObjects\Payload;
use JCSoriano\LaravelCrudStubs\LaravelCrudStubs;

class MigrationGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $modelName = $model->model()->studlyCase();
        $tableName = $model->model()->pluralSnakeCase();

        $directory = database_path('migrations');
        $this->createDirectoryIfNotExists($directory);

        $timestamp = now()->format('Y_m_d_His');
        $fileName = $timestamp.'_create_'.$tableName.'_table';

        $migrationsPrinter = LaravelCrudStubs::buildPrinter('migrations');
        $output = $migrationsPrinter->print($payload->fields);

        LaravelStub::from($this->getStubPath('crud.migration.stub'))
            ->to($directory)
            ->name($fileName)
            ->ext('php')
            ->replaces([
                ...$payload->variables(),
                'MIGRATION_FIELDS' => $output->output,
            ])
            ->conditions($payload->conditions)
            ->generate();

        $this->printSuccess('Migration', $directory, $fileName, $payload);

        return $payload;
    }
}
