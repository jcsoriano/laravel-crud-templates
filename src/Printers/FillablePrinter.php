<?php

namespace JCSoriano\LaravelCrudStubs\Printers;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use JCSoriano\LaravelCrudStubs\DataObjects\Field;
use JCSoriano\LaravelCrudStubs\DataObjects\Output;

class FillablePrinter implements Printer
{
    public function print(Collection $fields): Output
    {
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

        return new Output($fillable->join(",\n        "));
    }
}
