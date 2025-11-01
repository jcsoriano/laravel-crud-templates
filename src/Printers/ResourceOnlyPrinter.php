<?php

namespace JCSoriano\CrudTemplates\Printers;

use JCSoriano\CrudTemplates\DataObjects\Field;
use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\DataObjects\Payload;

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
