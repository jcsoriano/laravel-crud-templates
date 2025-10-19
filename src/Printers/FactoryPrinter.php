<?php

namespace JCSoriano\LaravelCrudTemplates\Printers;

use JCSoriano\LaravelCrudTemplates\DataObjects\Field;
use JCSoriano\LaravelCrudTemplates\DataObjects\Output;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

class FactoryPrinter implements Printer
{
    public function print(Payload $payload): Output
    {
        $fields = $payload->fields;
        $factoryFields = collect();
        $namespaces = collect();

        /** @var Field $field */
        foreach ($fields as $field) {
            $fieldType = new $field->typeClass($field);

            if (method_exists($fieldType, 'factory')) {
                $output = $fieldType->factory();
                $fakeData = $output->output;
                $factoryFields->push("'{$field->name->snakeCase()}' => {$fakeData},");

                // Merge namespaces from the factory output
                if ($output->namespaces) {
                    $namespaces = $namespaces->merge($output->namespaces);
                }
            }
        }

        return new Output($factoryFields->join("\n            "), $namespaces);
    }
}
