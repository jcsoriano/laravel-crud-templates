<?php

namespace JCSoriano\LaravelCrudStubs\Printers;

use JCSoriano\LaravelCrudStubs\DataObjects\Field;
use JCSoriano\LaravelCrudStubs\DataObjects\Output;
use JCSoriano\LaravelCrudStubs\DataObjects\Payload;

class RulesPrinter implements Printer
{
    public function print(Payload $payload): Output
    {
        $fields = $payload->fields;
        $rules = collect();
        $namespaces = collect();

        /** @var Field $field */
        foreach ($fields as $field) {
            $fieldType = new $field->typeClass($field);

            if (method_exists($fieldType, 'rule')) {
                $output = $fieldType->rule();
                $rules->push($output->output);
                $namespaces = $namespaces->merge($output->namespaces);
            }
        }

        return new Output($rules->join(",\n            "), $namespaces);
    }
}
