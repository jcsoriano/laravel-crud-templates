<?php

namespace JCSoriano\CrudTemplates\Printers;

use JCSoriano\CrudTemplates\DataObjects\Field;
use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\DataObjects\Payload;

class CastsPrinter implements Printer
{
    public function print(Payload $payload): Output
    {
        $fields = $payload->fields;
        $casts = collect();
        $namespaces = collect();

        /** @var Field $field */
        foreach ($fields as $field) {
            $fieldType = new $field->typeClass($field);

            if (method_exists($fieldType, 'cast')) {
                $output = $fieldType->cast();
                $casts->push($output->output);
                $namespaces = $output->mergeNamespaces($namespaces);
            }
        }

        // Only output the method if there are casts
        if ($casts->isEmpty()) {
            return new Output('', $namespaces);
        }

        $castsString = $casts->join(",\n            ");
        $methodOutput = <<<OUTPUT
    protected function casts(): array
    {
        return [
            {$castsString}
        ];
    }
OUTPUT;

        return new Output($methodOutput, $namespaces);
    }
}
