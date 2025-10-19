<?php

namespace JCSoriano\LaravelCrudStubs\Printers;

use JCSoriano\LaravelCrudStubs\DataObjects\Field;
use JCSoriano\LaravelCrudStubs\DataObjects\Output;
use JCSoriano\LaravelCrudStubs\DataObjects\Payload;

class ResourceRelationPrinter implements Printer
{
    public function print(Payload $payload): Output
    {
        $fields = $payload->fields;
        $relations = collect();
        $namespaces = collect();

        /** @var Field $field */
        foreach ($fields as $field) {
            $fieldType = new $field->typeClass($field);

            if (method_exists($fieldType, 'resourceRelations')) {
                $result = $fieldType->resourceRelations();
                foreach ($result as $key => $value) {
                    $relations->push("'{$key}' => {$value},");
                }

                // Add resource class namespace for relations
                $resourceName = $field->name->studlyCase();
                $namespaces->push("App\\Http\\Resources\\{$resourceName}Resource");
            }
        }

        return new Output($relations->join("\n            "), $namespaces);
    }
}
