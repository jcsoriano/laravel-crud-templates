<?php

namespace JCSoriano\LaravelCrudStubs\Printers;

use Illuminate\Support\Arr;
use JCSoriano\LaravelCrudStubs\DataObjects\Field;
use JCSoriano\LaravelCrudStubs\DataObjects\Output;
use JCSoriano\LaravelCrudStubs\DataObjects\Payload;

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
