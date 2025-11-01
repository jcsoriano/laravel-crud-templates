# Customizing Printers

Printers are responsible for generating specific parts of code within templates, such as fillable arrays, casts, validation rules, and migrations. By customizing printers, you can modify how these code segments are generated without changing the entire generator.

## Understanding Printers

CRUD Templates for Laravel includes these built-in printers:

- `casts` - Model type casts
- `factory` - Factory field definitions
- `fillable` - Fillable array for models
- `migrations` - Migration column definitions
- `relations` - Relationship methods for models
- `resource` - Resource fields and relationships
- `rules` - Validation rules
- `dbAssertions` - Database assertion columns for tests

## How Printers Work

Printers are used by generators to produce specific code segments. They receive a `Payload` object containing model and field information, and return an `Output` object with the generated code.

## Creating a Custom Printer

### Quick Start: Using the Make Command

The fastest way to create a new printer is using the `make:printer` command:

```bash
php artisan make:printer CustomFillable
```

This command will:
1. Create a printer class at `app/Printers/{Name}Printer.php`
2. Automatically register it in `app/Providers/CrudTemplatesServiceProvider.php`

::: tip
Use `--force` to overwrite an existing printer file.
:::

If you want to understand what the command generated or customize it further, continue reading the manual creation steps below.

### Step 1: Create the Printer Class

Create a class that implements the `Printer` interface:

```php
<?php

namespace App\Printers;

use JCSoriano\CrudTemplates\Printers\Printer;
use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\DataObjects\Payload;

class CustomFillablePrinter implements Printer
{
    public function print(Payload $payload): Output
    {
        $fields = $payload->fields;
        $fillableFields = collect();
        
        foreach ($fields as $field) {
            $fieldType = new $field->typeClass($field);
            
            if (method_exists($fieldType, 'fillable')) {
                $result = $fieldType->fillable();
                if ($result) {
                    $fillableFields->push("'{$result->output}'");
                }
            }
        }
        
        return new Output($fillableFields->join(",\n        "));
    }
}
```

### Step 2: Register the Printer

Register your printer in a service provider's `register()` method:

```php
use App\Printers\CustomFillablePrinter;

public function register()
{
    $this->app->bind('crud-templates::printer::fillable', CustomFillablePrinter::class);
}
```

**Note:** To override an existing printer, bind to the same key as the package default (e.g., `crud-templates::printer::fillable`).

### Step 3: The Printer is Used Automatically

Once registered, your custom printer will be used automatically by the model generator when it needs to generate fillable fields.

## The Printer Interface

All printers must implement the `Printer` interface:

```php
interface Printer
{
    public function print(Payload $payload): Output;
}
```

## The Output Object

Printers return an `Output` object that can contain:

- **output**: The generated code string
- **namespaces**: Optional namespace collection. Used by Generators to collect namespaces, deduplicate them, sort alphabetically, and place them at the top of the file.

## Practical Example: Searchable Fields Printer

Create a printer for Laravel Scout searchable fields:

```php
<?php

namespace App\Printers;

use JCSoriano\CrudTemplates\Printers\Printer;
use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\DataObjects\Payload;

class SearchablePrinter implements Printer
{
    public function print(Payload $payload): Output
    {
        $fields = $payload->fields;
        $searchableFields = collect();
        
        // Only include string and text fields as searchable
        foreach ($fields as $field) {
            if (in_array($field->type->name, ['string', 'text'])) {
                $searchableFields->push("'{$field->name->snakeCase()}'");
            }
        }
        
        return new Output($searchableFields->join(",\n            "));
    }
}
```

Register it in your service provider's `register()` method:

```php
$this->app->bind('crud-templates::printer::searchable', SearchablePrinter::class);
```

Use in a custom model stub:

::: v-pre
```php
public function toSearchableArray(): array
{
    return $this->only([
        {{ SEARCHABLE }}
    ]);
}
```
:::

## Using Printers in Custom Generators

You can use printers in your custom generators:

```php
public function variables(Payload $payload): array
{
    $fillableOutput = $this->print('fillable', $payload);
    $fillableFields = $fillableOutput->output;
    
    // Add to stub variables
    return [
        ...$payload->variables(),
        'FILLABLE' => $fillableFields,
    ];
}
```

## Next Steps

- Check the package source code to dive deeper
