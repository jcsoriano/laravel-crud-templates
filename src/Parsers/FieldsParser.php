<?php

namespace JCSoriano\LaravelCrudStubs\Parsers;

use Illuminate\Support\Collection;
use JCSoriano\LaravelCrudStubs\DataObjects\Field;
use JCSoriano\LaravelCrudStubs\DataObjects\Name;
use JCSoriano\LaravelCrudStubs\Exceptions\InvalidFieldsException;
use JCSoriano\LaravelCrudStubs\LaravelCrudStubs;

class FieldsParser
{
    public function parse(?string $fieldsString): Collection
    {
        if (empty($fieldsString)) {
            return collect();
        }

        $fields = collect();
        $fieldDefinitions = explode(',', $fieldsString);

        foreach ($fieldDefinitions as $fieldDefinition) {
            $fieldDefinition = trim($fieldDefinition);

            if (empty($fieldDefinition)) {
                continue;
            }

            $fields->push($this->parseField($fieldDefinition));
        }

        return $fields;
    }

    protected function parseField(string $fieldDefinition): Field
    {
        $parts = explode(':', $fieldDefinition);

        if (count($parts) < 2) {
            throw new InvalidFieldsException("Invalid field syntax: {$fieldDefinition}. Expected format: field_name:type");
        }

        $fieldName = trim($parts[0]);
        $fieldType = trim($parts[1]);
        $options = [];

        // Check for nullable fields (? after field name)
        $required = true;
        if (str_ends_with($fieldName, '?')) {
            $required = false;
            $fieldName = rtrim($fieldName, '?');
        }

        if (empty($fieldName)) {
            throw new InvalidFieldsException("Field name cannot be empty in: {$fieldDefinition}");
        }

        // Handle enum with class specification
        if ($fieldType === 'enum') {
            if (count($parts) < 3) {
                throw new InvalidFieldsException('Enum fields require a class specification: field_name:enum:EnumClass');
            }
            $options['enumClass'] = trim($parts[2]);
        }

        $fieldTypes = LaravelCrudStubs::getFieldTypes();

        if (! array_key_exists($fieldType, $fieldTypes)) {
            throw new InvalidFieldsException("Unsupported field type: {$fieldType}");
        }

        return new Field(
            name: new Name($fieldName),
            required: $required,
            typeClass: $fieldTypes[$fieldType],
            options: $options,
        );
    }
}
