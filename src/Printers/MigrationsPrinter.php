<?php

namespace JCSoriano\LaravelCrudStubs\Printers;

use JCSoriano\LaravelCrudStubs\DataObjects\Field;
use JCSoriano\LaravelCrudStubs\DataObjects\Output;
use JCSoriano\LaravelCrudStubs\DataObjects\Payload;

class MigrationsPrinter implements Printer
{
    public function print(Payload $payload): Output
    {
        $fields = $payload->fields;
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

        // Add scope-based migrations
        $scope = $payload->options['scope'] ?? null;
        if ($scope === 'user') {
            $migrations->push("            \$table->foreignId('user_id')->constrained();");
        } elseif ($scope === 'team') {
            $migrations->push("            \$table->foreignId('team_id')->constrained();");
        }

        return new Output($migrations->join("\n"), $namespaces);
    }
}
