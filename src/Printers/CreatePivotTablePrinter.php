<?php

namespace JCSoriano\CrudTemplates\Printers;

use Illuminate\Support\Facades\Schema;
use JCSoriano\CrudTemplates\DataObjects\Field;
use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\DataObjects\Payload;

class CreatePivotTablePrinter implements Printer
{
    public function print(Payload $payload): Output
    {
        $fields = $payload->fields;
        $pivotTables = collect();
        $currentModelName = $payload->model->model()->studlyCase();

        /** @var Field $field */
        foreach ($fields as $field) {
            $fieldType = new $field->typeClass($field);

            if (method_exists($fieldType, 'createPivotTable')) {
                $tableName = $fieldType->pivotTableName($currentModelName);

                // Only create pivot table if it doesn't exist
                if (! Schema::hasTable($tableName)) {
                    $output = $fieldType->createPivotTable($currentModelName);
                    $pivotTables->push($output->output);
                }
            }
        }

        return new Output($pivotTables->join("\n\n"));
    }
}
