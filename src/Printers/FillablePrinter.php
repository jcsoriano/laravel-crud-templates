<?php

namespace JCSoriano\LaravelCrudTemplates\Printers;

use Illuminate\Support\Arr;
use JCSoriano\LaravelCrudTemplates\DataObjects\Field;
use JCSoriano\LaravelCrudTemplates\DataObjects\Output;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

class FillablePrinter implements Printer
{
    public function print(Payload $payload): Output
    {
        $fields = $payload->fields;
        $fillable = collect();

        /** @var Field $field */
        foreach ($fields as $field) {
            $fieldType = new $field->typeClass($field);

            if (method_exists($fieldType, 'fillable')) {
                foreach (Arr::wrap($fieldType->fillable()) as $column) {
                    $fillable->push("'{$column}'");
                }
            }
        }

        // Add scope-based fillables
        $scope = $payload->options['scope'] ?? null;
        if ($scope === 'user') {
            $fillable->push("'user_id'");
        } elseif ($scope === 'team') {
            $fillable->push("'team_id'");
        }

        return new Output($fillable->join(",\n        "));
    }
}
