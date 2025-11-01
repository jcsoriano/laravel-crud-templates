<?php

namespace JCSoriano\CrudTemplates\Printers;

use JCSoriano\CrudTemplates\DataObjects\Field;
use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\DataObjects\Payload;

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
                $column = method_exists($fieldType, 'column')
                    ? $fieldType->column()
                    : $field->name->snakeCase();
                $factoryFields->push("'{$column}' => {$fakeData},");

                // Merge namespaces from the factory output
                if ($output->namespaces) {
                    $namespaces = $namespaces->merge($output->namespaces);
                }
            }
        }

        return new Output($factoryFields->join("\n            "), $namespaces);
    }
}
