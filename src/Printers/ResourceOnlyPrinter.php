<?php

namespace JCSoriano\LaravelCrudStubs\Printers;

use JCSoriano\LaravelCrudStubs\DataObjects\Field;
use JCSoriano\LaravelCrudStubs\DataObjects\Output;
use JCSoriano\LaravelCrudStubs\DataObjects\Payload;

class ResourceOnlyPrinter implements Printer
{
    public function print(Payload $payload): Output
    {
        $fields = $payload->fields;
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
