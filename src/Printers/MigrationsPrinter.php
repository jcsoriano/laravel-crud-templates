<?php

namespace JCSoriano\LaravelCrudStubs\Printers;

use Illuminate\Support\Collection;
use JCSoriano\LaravelCrudStubs\DataObjects\Field;
use JCSoriano\LaravelCrudStubs\DataObjects\Output;

class MigrationsPrinter implements Printer
{
    public function print(Collection $fields): Output
    {
        $migrations = collect();
        $namespaces = collect();

        /** @var Field $field */
        foreach ($fields as $field) {
            $fieldType = new $field->typeClass($field);

            if (method_exists($fieldType, 'migration')) {
                $output = $fieldType->migration();
                $migrations->push('            '.$output->output);
                $namespaces = $namespaces->merge($output->namespaces);
            }
        }

        return new Output($migrations->join("\n"), $namespaces);
    }
}
