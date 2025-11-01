<?php

namespace JCSoriano\CrudTemplates\Printers;

use JCSoriano\CrudTemplates\DataObjects\Field;
use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\DataObjects\Payload;

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
