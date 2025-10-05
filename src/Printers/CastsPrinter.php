<?php

namespace JCSoriano\LaravelCrudStubs\Printers;

use Illuminate\Support\Collection;
use JCSoriano\LaravelCrudStubs\DataObjects\Field;
use JCSoriano\LaravelCrudStubs\DataObjects\Output;

class CastsPrinter implements Printer
{
    public function print(Collection $fields): Output
    {
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
