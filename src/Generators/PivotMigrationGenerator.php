<?php

namespace JCSoriano\CrudTemplates\Generators;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use JCSoriano\CrudTemplates\DataObjects\Field;
use JCSoriano\CrudTemplates\DataObjects\Payload;

class PivotMigrationGenerator extends MigrationGenerator
{
    protected function shouldSkipGeneration(Payload $payload): bool
    {
        $output = $this->print('createPivotTables', $payload);

        if (empty($output->output)) {
            $payload->components->info('No pivot tables to create. Skipping pivot migration generation.');

            return true;
        }

        return false;
    }

    protected function variables(Payload $payload): array
    {
        $createOutput = $this->print('createPivotTables', $payload);
        $dropOutput = $this->print('dropPivotTables', $payload);

        return [
            ...$payload->variables(),
            'CREATE_PIVOT_TABLES' => $createOutput->output,
            'DROP_PIVOT_TABLES' => $dropOutput->output,
        ];
    }

    protected function stubPath(Payload $payload): string
    {
        return 'api/migration.pivot.stub';
    }

    protected function fileName(Payload $payload): string
    {
        $timestamp = now()->format('Y_m_d_His');
        $pivotTableNames = $this->collectPivotTableNames($payload);

        $joinedNames = $pivotTableNames->join('_');

        return $timestamp.'_create_'.$joinedNames.'_table';
    }

    protected function fileType(Payload $payload): string
    {
        return 'Pivot Migration';
    }

    protected function collectPivotTableNames(Payload $payload): Collection
    {
        $fields = $payload->fields;
        $currentModelName = $payload->model->model()->studlyCase();
        $pivotTableNames = collect();

        /** @var Field $field */
        foreach ($fields as $field) {
            $fieldType = new $field->typeClass($field);

            if (method_exists($fieldType, 'pivotTableName')) {
                $tableName = $fieldType->pivotTableName($currentModelName);

                // Only include tables that don't exist
                if (! Schema::hasTable($tableName)) {
                    $pivotTableNames->push($tableName);
                }
            }
        }

        return $pivotTableNames;
    }
}
