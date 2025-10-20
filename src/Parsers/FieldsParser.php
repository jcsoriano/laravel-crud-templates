<?php

namespace JCSoriano\LaravelCrudTemplates\Parsers;

use Illuminate\Support\Collection;
use JCSoriano\LaravelCrudTemplates\DataObjects\Field;
use JCSoriano\LaravelCrudTemplates\DataObjects\Model;
use JCSoriano\LaravelCrudTemplates\DataObjects\Name;
use JCSoriano\LaravelCrudTemplates\Exceptions\InvalidFieldsException;

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

        // Handle relationships with optional model class specification
        $relationshipTypes = [
            'belongsTo', 'hasMany', 'belongsToMany', 'morphMany', 'morphToMany',
        ];
        $modelClass = in_array($fieldType, $relationshipTypes) && count($parts) >= 3
            ? trim($parts[2])
            : null;

        return new Field(
            name: new Name($fieldName),
            required: $required,
            // Resolve the field type class from the container binding
            typeClass: app("laravel-crud-templates::field-type::{$fieldType}"),
            options: $options,
            model: $modelClass ? new Model($modelClass) : null,
        );
    }
}
