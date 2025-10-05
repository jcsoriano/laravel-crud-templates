<?php

namespace JCSoriano\LaravelCrudStubs\Printers;

use Illuminate\Support\Collection;
use JCSoriano\LaravelCrudStubs\DataObjects\Field;
use JCSoriano\LaravelCrudStubs\DataObjects\Output;

class ResourceOnlyPrinter implements Printer
{
    public function print(Collection $fields): Output
    {
        $onlyFields = collect(['id']);

        /** @var Field $field */
        foreach ($fields as $field) {
            $fieldType = new $field->typeClass($field);

            if (method_exists($fieldType, 'resourceOnly')) {
                $result = $fieldType->resourceOnly();
                foreach ($result as $fieldName) {
                    $onlyFields->push($fieldName);
                }
            }
        }

        // Add timestamps
        $onlyFields->push('created_at', 'updated_at');

        return new Output($onlyFields->map(fn ($field) => "'{$field}'")->join(",\n                "));
    }
}
