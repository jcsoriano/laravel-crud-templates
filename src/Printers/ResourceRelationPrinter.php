<?php

namespace JCSoriano\LaravelCrudStubs\Printers;

use Illuminate\Support\Collection;
use JCSoriano\LaravelCrudStubs\DataObjects\Field;
use JCSoriano\LaravelCrudStubs\DataObjects\Output;

class ResourceRelationPrinter implements Printer
{
    public function print(Collection $fields): Output
    {
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
