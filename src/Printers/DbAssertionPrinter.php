<?php

namespace JCSoriano\LaravelCrudTemplates\Printers;

use JCSoriano\LaravelCrudTemplates\DataObjects\Field;
use JCSoriano\LaravelCrudTemplates\DataObjects\Output;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

class DbAssertionPrinter implements Printer
{
    public function print(Payload $payload): Output
    {
        $fields = $payload->fields;
        $dbAssertions = collect();
        $namespaces = collect();

        /** @var Field $field */
        foreach ($fields as $field) {
            $fieldType = new $field->typeClass($field);

            if (method_exists($fieldType, 'dbAssertion')) {
                $output = $fieldType->dbAssertion();
                $dbAssertions->push($output->output);
                $namespaces = $namespaces->merge($output->namespaces);
            }
        }

        return new Output($dbAssertions->join(",\n            "), $namespaces);
    }
}
