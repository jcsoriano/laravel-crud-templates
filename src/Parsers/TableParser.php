<?php

namespace JCSoriano\LaravelCrudStubs\Parsers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use JCSoriano\LaravelCrudStubs\DataObjects\Field;
use JCSoriano\LaravelCrudStubs\DataObjects\Name;
use JCSoriano\LaravelCrudStubs\LaravelCrudStubs;

class TableParser
{
    public function parse(?string $tableName): Collection
    {
        if (empty($tableName) || ! Schema::hasTable($tableName)) {
            return collect();
        }

        $columns = Schema::getColumnListing($tableName);
        $fields = collect();

        foreach ($columns as $column) {
            // Skip common Laravel columns
            if (in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            $columnType = Schema::getColumnType($tableName, $column);
            $fieldType = $this->mapColumnTypeToFieldType($columnType);

            if ($fieldType === null) {
                continue; // Skip unsupported column types
            }

            $fieldTypes = LaravelCrudStubs::getFieldTypes();

            $fields->push(new Field(
                name: new Name($column),
                required: true, // We can't easily determine nullability from Schema
                typeClass: $fieldTypes[$fieldType],
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
}
