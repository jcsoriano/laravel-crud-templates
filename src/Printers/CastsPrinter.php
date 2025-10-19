<?php

namespace JCSoriano\LaravelCrudTemplates\Printers;

use JCSoriano\LaravelCrudTemplates\DataObjects\Field;
use JCSoriano\LaravelCrudTemplates\DataObjects\Output;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

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
                $namespaces = $namespaces->merge($output->namespaces);
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
