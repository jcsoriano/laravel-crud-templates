<?php

namespace JCSoriano\CrudTemplates\Printers;

use Illuminate\Support\Facades\Schema;
use JCSoriano\CrudTemplates\DataObjects\Field;
use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\DataObjects\Payload;

class DropPivotTablePrinter implements Printer
{
    public function print(Payload $payload): Output
    {
        $fields = $payload->fields;
        $dropStatements = collect();
        $currentModelName = $payload->model->model()->studlyCase();

        /** @var Field $field */
        foreach ($fields as $field) {
            $fieldType = new $field->typeClass($field);

            if (method_exists($fieldType, 'dropPivotTable')) {
                $tableName = $fieldType->pivotTableName($currentModelName);

                if (! Schema::hasTable($tableName)) {
                    $output = $fieldType->dropPivotTable($currentModelName);
                    $dropStatements->push($output->output);
                }
            }
        }

        return new Output($dropStatements->join("\n        "));
    }
}
