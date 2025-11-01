<?php

namespace JCSoriano\CrudTemplates\Printers;

use JCSoriano\CrudTemplates\DataObjects\Field;
use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\DataObjects\Payload;

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
