<?php

namespace JCSoriano\LaravelCrudTemplates\Printers;

use JCSoriano\LaravelCrudTemplates\DataObjects\Field;
use JCSoriano\LaravelCrudTemplates\DataObjects\Output;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

class RelationsPrinter implements Printer
{
    public function print(Payload $payload): Output
    {
        $fields = $payload->fields;
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

        // Add scope-based relations
        $scope = $payload->options['scope'] ?? null;
        if ($scope === 'user') {
            $relations->push("    public function user()\n    {\n        return \$this->belongsTo(User::class);\n    }");
            $namespaces->push('App\\Models\\User');
        } elseif ($scope === 'team') {
            $relations->push("    public function team()\n    {\n        return \$this->belongsTo(Team::class);\n    }");
            $namespaces->push('App\\Models\\Team');
        }

        return new Output($relations->join("\n\n"), $namespaces);
    }
}
