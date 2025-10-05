<?php

namespace JCSoriano\LaravelCrudStubs\Printers;

use Illuminate\Support\Collection;
use JCSoriano\LaravelCrudStubs\DataObjects\Field;
use JCSoriano\LaravelCrudStubs\DataObjects\Output;

class RelationsPrinter implements Printer
{
    public function print(Collection $fields): Output
    {
        $relations = collect();
        $namespaces = collect();

        /** @var Field $field */
        foreach ($fields as $field) {
            $fieldType = new $field->typeClass($field);

            if (method_exists($fieldType, 'relation')) {
                $output = $fieldType->relation();
                $relations->push($output->output);
                $namespaces = $namespaces->merge($output->namespaces);
            }
        }

        return new Output($relations->join("\n\n"), $namespaces);
    }
}
