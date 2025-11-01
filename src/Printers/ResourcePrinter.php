<?php

namespace JCSoriano\CrudTemplates\Printers;

use JCSoriano\CrudTemplates\DataObjects\Field;
use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\DataObjects\Payload;

class ResourcePrinter implements Printer
{
    public function print(Payload $payload): Output
    {
        $fields = $payload->fields;
        $resourceFields = collect();
        $namespaces = collect();

        // Always start with id
        $resourceFields->push("'id' => \$this->id,");

        /** @var Field $field */
        foreach ($fields as $field) {
            $fieldType = new $field->typeClass($field);

            if (method_exists($fieldType, 'resource')) {
                $result = $fieldType->resource();
                foreach ($result as $key => $value) {
                    $resourceFields->push("'{$key}' => {$value},");
                }

                // Collect namespaces from relationship resources
                if (str_contains($value, 'Resource::')) {
                    $relatedNamespace = $field->relatedClass('App\\Http\\Resources');
                    $namespaces->push($relatedNamespace.'Resource');
                }
            }
        }

        // Add timestamps
        $resourceFields->push("'created_at' => \$this->created_at,");
        $resourceFields->push("'updated_at' => \$this->updated_at,");

        return new Output($resourceFields->join("\n            "), $namespaces);
    }
}
