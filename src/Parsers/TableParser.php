<?php

namespace JCSoriano\CrudTemplates\Parsers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use JCSoriano\CrudTemplates\DataObjects\Field;
use JCSoriano\CrudTemplates\DataObjects\Name;

class TableParser
{
    public function parse(?string $tableName): Collection
    {
        if (empty($tableName) || ! Schema::hasTable($tableName)) {
            return collect();
        }

        $columns = Schema::getColumnListing($tableName);
        $fields = collect();
        $polymorphicPairs = $this->detectPolymorphicPairs($tableName, $columns);

        foreach ($columns as $column) {
            // Skip common Laravel columns
            if (in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            // Skip columns that are part of polymorphic pairs
            if ($this->isPartOfPolymorphicPair($column, $polymorphicPairs)) {
                continue;
            }

            $columnType = Schema::getColumnType($tableName, $column);
            $fieldType = $this->mapColumnTypeToFieldType($columnType);

            if ($fieldType === null) {
                continue; // Skip unsupported column types
            }

            $nullable = $this->isNullable($tableName, $column);

            $fields->push(new Field(
                name: new Name($column),
                required: ! $nullable,
                // Resolve the field type class from the container binding
                typeClass: app("crud-templates::field-type::{$fieldType}"),
                options: [],
            ));
        }

        // Add polymorphic fields
        foreach ($polymorphicPairs as $baseName => $nullable) {
            $fields->push(new Field(
                name: new Name($baseName),
                required: ! $nullable,
                // Resolve the field type class from the container binding
                typeClass: app('crud-templates::field-type::morphTo'),
                options: [],
            ));
        }

        return $fields;
    }

    protected function mapColumnTypeToFieldType(string $columnType): ?string
    {
        return match ($columnType) {
            'string', 'varchar' => 'string',
            'integer', 'bigint', 'int' => 'integer',
            'decimal', 'float', 'double' => 'decimal',
            'date' => 'date',
            'datetime', 'timestamp' => 'datetime',
            'text', 'longtext', 'mediumtext' => 'text',
            'boolean', 'tinyint' => 'boolean',
            'json' => 'json',
            default => null,
        };
    }

    protected function detectPolymorphicPairs(string $tableName, array $columns): array
    {
        $pairs = [];

        foreach ($columns as $column) {
            // Check if this is a _type column
            if (str_ends_with($column, '_type')) {
                $baseName = substr($column, 0, -5); // Remove '_type'
                $idColumn = $baseName.'_id';

                // Check if corresponding _id column exists
                if (in_array($idColumn, $columns)) {
                    // Check if both are nullable or both are not nullable
                    $typeNullable = $this->isNullable($tableName, $column);
                    $idNullable = $this->isNullable($tableName, $idColumn);

                    // Store the pair with nullable status (both should have same nullability)
                    $pairs[$baseName] = $typeNullable && $idNullable;
                }
            }
        }

        return $pairs;
    }

    protected function isPartOfPolymorphicPair(string $column, array $polymorphicPairs): bool
    {
        foreach (array_keys($polymorphicPairs) as $baseName) {
            if ($column === $baseName.'_type' || $column === $baseName.'_id') {
                return true;
            }
        }

        return false;
    }

    protected function isNullable(string $tableName, string $column): bool
    {
        $connection = Schema::getConnection();
        $table = $connection->getDoctrineSchemaManager()->listTableDetails($tableName);
        $columnDetails = $table->getColumn($column);

        return ! $columnDetails->getNotnull();
    }
}
