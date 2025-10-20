<?php

namespace JCSoriano\LaravelCrudTemplates\Printers;

use JCSoriano\LaravelCrudTemplates\DataObjects\Field;
use JCSoriano\LaravelCrudTemplates\DataObjects\Output;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

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
                $relatedNamespace = $field->relatedClass('App\\Http\\Resources');
                $namespaces->push($relatedNamespace.'Resource');
            }
        }

        return new Output($relations->join("\n            "), $namespaces);
    }
}
