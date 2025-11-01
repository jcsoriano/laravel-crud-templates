# Customizing Field Types

CRUD Templates for Laravel already comes with many built-in [Field Types](/guide/field-types). However, sometimes you may need special field types specific to your project, or would like to customize the behavior of existing field types. This section will guide you through creating and customizing field types.

## Understanding Field Types

A field type controls:

- **Migration**: How the column is defined in the database
- **Validation**: What rules are applied to the field
- **Model Cast**: How the value is cast in the model
- **Factory**: How fake data is generated for testing
- **Resource**: How the field appears in API responses
- **Fillable**: Whether the field is mass-assignable
- **Relations**: Whether the field creates a relationship method

## Creating a Custom Field Type

### Step 1: Create the Field Type Class

Create a new class that extends `FieldType`:

```php
<?php

namespace App\FieldTypes;

use JCSoriano\CrudTemplates\DataObjects\Output;
use JCSoriano\CrudTemplates\FieldTypes\FieldType;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasSimpleMigration;
use JCSoriano\CrudTemplates\FieldTypes\Traits\HasSimpleRule;
use JCSoriano\CrudTemplates\FieldTypes\Traits\IsFillable;
use JCSoriano\CrudTemplates\FieldTypes\Traits\IsSimpleResourceField;

class PhoneType extends FieldType
{
    use HasSimpleMigration;
    use HasSimpleRule;
    use IsFillable;
    use IsSimpleResourceField;

    public function migration(): Output
    {
        return $this->buildSimpleMigration('string');
    }

    public function rule(): Output
    {
        return $this->buildSimpleRule(['string', 'regex:/^[+]?[0-9]{10,15}$/']);
    }

    public function factory(): Output
    {
        return new Output("fake()->regexify('[+][0-9]{11}')");
    }
}
```

### Step 2: Register the Field Type

Register your field type in a service provider's `register()` method (e.g., `AppServiceProvider`):

```php
use App\FieldTypes\PhoneType;

public function register()
{
    $this->app->bind('crud-templates::field-type::phone', PhoneType::class);
}
```

**Note:** To override an existing field type, bind to the same key as the package default (e.g., `crud-templates::field-type::string`).

### Step 3: Use Your Custom Field Type

Now you can use it in CRUD generation:

```bash
php artisan crud:generate Contact --fields="name:string,phone:phone,mobile?:phone"
```

## Field Type Methods

### Required Methods

#### migration()

Defines how the database column is created.

```php
public function migration(): Output
{
    $field = $this->field;
    $nullable = $field->required ? '' : '->nullable()';
    
    return new Output(
        "\$table->uuid('{$field->name->snakeCase()}'){$nullable};"
    );
}
```

#### rule()

Defines validation rules for the field.

```php
public function rule(): Output
{
    $fieldName = $this->field->name->snakeCase();
    $required = $this->field->required ? 'required' : 'nullable';
    
    return new Output("'{$fieldName}' => ['{$required}', 'uuid']");
}
```

#### factory()

Defines how fake data is generated for testing. Returns an `Output` object that can include namespaces for dependencies.

```php
public function factory(): Output
{
    return new Output('fake()->uuid()');
}
```

**With Namespaces (for relationships or enums):**

The second parameter to `Output` is a collection of namespaces to import. This is useful when your factory needs to reference other models or classes.

```php
public function factory(): Output
{
    $modelName = $this->field->name->studlyCase();
    $modelClass = "App\\Models\\{$modelName}";
    
    return new Output(
        "{$modelName}::factory()",
        collect([$modelClass])
    );
}
```

### Optional Methods

#### cast()

Defines model casting (returns `Output` or `null`).

```php
public function cast(): ?Output
{
    $fieldName = $this->field->name->snakeCase();

    return new Output("'{$fieldName}' => 'string'");
}
```

#### fillable()

Whether the field is mass-assignable (returns `string`).

```php
public function fillable(): string
{
    return $this->field->name->snakeCase();
}
```

#### resourceOnly()

Specifies which fields to include in the resource's `only()` array (returns `array`).

```php
public function resourceOnly(): array
{
    return [$this->field->name->snakeCase()];
}
```

#### resourceRelations()

For relationship fields, defines how to include related resources (returns `array`).

```php
public function resourceRelations(): array
{
    $fieldName = $this->field->name->snakeCase();
    $relationName = $this->field->name->camelCase();
    $modelName = $this->getModelName();
    
    return [
        $fieldName => "{$modelName}Resource::make(\$this->whenLoaded('{$relationName}'))",
    ];
}
```

#### dbAssertion()

Defines how to assert this field in database tests (returns `Output`).

```php
public function dbAssertion(): Output
{
    $column = $this->field->name->snakeCase();
    return new Output("'{$column}' => \$payload['{$column}']");
}
```

#### column()

For relationship fields, returns the database column name (usually `field_name_id`):

```php
public function column(): string
{
    return $this->field->name->snakeCase() . '_id';
}
```

#### relation()

Defines relationship methods (returns `Output` or `null`).

```php
public function relation(): ?Output
{
    $relationName = $this->field->name->camelCase();
    $modelName = $this->getModelName();
    $modelClass = $this->getModelClass();
    
    $output = <<<OUTPUT
    public function {$relationName}(): BelongsTo
    {
        return \$this->belongsTo({$modelName}::class);
    }
OUTPUT;

    $namespaces = collect([
        $modelClass,
        'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
    ]);
    
    return new Output($output, $namespaces);
}
```

## Helper Traits

The package provides several traits to simplify field type creation:

### HasSimpleMigration

Creates basic migration syntax:

```php
use HasSimpleMigration;

public function migration(): Output
{
    return $this->buildSimpleMigration('string');
    // Produces: $table->string('field_name');
}
```

### HasSimpleRule

Creates basic validation rules. Accepts either a string or an array of rules:

```php
use HasSimpleRule;

public function rule(): Output
{
    // With array
    return $this->buildSimpleRule(['string', 'max:255']);
    // Produces: 'field_name' => ['required', 'string', 'max:255']
    
    // With string
    return $this->buildSimpleRule('uuid');
    // Produces: 'field_name' => ['required', 'uuid']
}
```

### IsFillable

Makes the field mass-assignable and provides database assertion support:

```php
use IsFillable;

// Automatically implements:
// - fillable(): Returns the field name for mass assignment
// - dbAssertion(): Returns Output for database testing assertions
```

### IsSimpleResourceField

Adds field to API resources:

```php
use IsSimpleResourceField;

// Automatically implements:
// - resourceOnly(): Returns array with the field name for the only() method
```

### ParsesRelatedModel

Helper trait for relationship field types to parse related model information:

```php
use ParsesRelatedModel;

// Provides methods:
// - getModelClass(): Returns the fully qualified model class name
// - getModelName(): Returns the model name in StudlyCase

public function factory(): Output
{
    $modelName = $this->getModelName();
    $modelClass = $this->getModelClass();
    
    return new Output(
        "{$modelName}::factory()",
        collect([$modelClass])
    );
}
```

### HasCast

Adds model casting:

```php
use HasCast;

public function cast(): ?Output
{
    return $this->buildCast('array');
    // Produces: 'field_name' => 'array'
}
```

## Overriding Existing Field Types

You can override existing field types by binding to the same container key in your service provider's `register()` method:

```php
use App\FieldTypes\CustomStringType;

public function register()
{
    $this->app->bind('crud-templates::field-type::string', CustomStringType::class);
}
```

Now all `string` fields will use your custom implementation.

## The Field Object

Inside your field type, you have access to `$this->field`, which contains:

```php
$this->field->name;       // Name object with various case formats
$this->field->required;   // bool: is field required?
$this->field->typeClass;  // string: the field type class name
$this->field->options;    // array: additional options (e.g., enum class)
$this->field->model;      // ?Model: related model (for relationship fields)
```

### Name Object Methods

The `Name` object provides various case conversion methods:

```php
$this->field->name->snakeCase();        // snake_case
$this->field->name->camelCase();        // camelCase
$this->field->name->studlyCase();       // StudlyCase
$this->field->name->kebabCase();        // kebab-case
$this->field->name->pluralSnakeCase();  // plural_snake_case
$this->field->name->pluralCamelCase();  // pluralCamelCase
$this->field->name->pluralStudlyCase(); // PluralStudlyCase
$this->field->name->pluralKebabCase();  // plural-kebab-case
```

## Next Steps

- Learn about [Customizing Printers](/templates/customizing-printers) to modify code snippets
- Check the package source code for more field type examples
