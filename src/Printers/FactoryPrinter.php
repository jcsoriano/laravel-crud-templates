<?php

namespace JCSoriano\LaravelCrudTemplates\Printers;

use JCSoriano\LaravelCrudTemplates\DataObjects\Field;
use JCSoriano\LaravelCrudTemplates\DataObjects\Output;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;
use JCSoriano\LaravelCrudTemplates\FieldTypes\EnumType;

class FactoryPrinter implements Printer
{
    public function print(Payload $payload): Output
    {
        $fields = $payload->fields;
        $factoryFields = collect();
        $namespaces = collect();

        /** @var Field $field */
        foreach ($fields as $field) {
            $fieldType = new $field->typeClass($field);

            if (method_exists($fieldType, 'factory')) {
                $fakeData = $fieldType->factory();
                $factoryFields->push("'{$field->name->snakeCase()}' => {$fakeData},");
            }

            if ($fieldType instanceof EnumType) {
                $enumClass = $field->options['enumClass'] ?? 'Enum';
                $namespaces->push("App\\Enums\\{$enumClass}");
            }
        }

        return new Output($factoryFields->join("\n            "), $namespaces);
    }
}
