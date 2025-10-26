# Customizing Field Types

Field types define how different data types are handled throughout the CRUD generation process. You can create custom field types to support new data types or override existing ones to change their behavior.

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

use JCSoriano\LaravelCrudTemplates\DataObjects\Output;
use JCSoriano\LaravelCrudTemplates\FieldTypes\FieldType;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\HasSimpleMigration;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\HasSimpleRule;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\IsFillable;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\IsSimpleResourceField;

class UuidType extends FieldType
{
    use HasSimpleMigration;
    use HasSimpleRule;
    use IsFillable;
    use IsSimpleResourceField;

    public function migration(): Output
    {
        return $this->buildSimpleMigration('uuid');
    }

    public function rule(): Output
    {
        return $this->buildSimpleRule('uuid');
    }

    public function factory(): Output
    {
        return new Output('fake()->uuid()');
    }
}
```

### Step 2: Register the Field Type

Register your field type in a service provider's `register()` method (e.g., `AppServiceProvider`):

```php
use App\FieldTypes\UuidType;

public function register()
{
    $this->app->bind('laravel-crud-templates::field-type::uuid', UuidType::class);
}
```

**Note:** To override an existing field type, bind to the same key as the package default (e.g., `laravel-crud-templates::field-type::string`).

### Step 3: Use Your Custom Field Type

Now you can use it in CRUD generation:

```bash
php artisan crud:generate Post --fields="identifier:uuid,title:string"
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
    $required = $this->field->required ? 'required' : 'nullable';
    
    return new Output(
        key: $this->field->name->snakeCase(),
        value: "{$required}|uuid"
    );
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
```php
public function factory(): Output
{
    $modelName = $this->field->name->studlyCase();
    
    return new Output(
        "{$modelName}::factory()",
        collect(["App\\Models\\{$modelName}"])
    );
}
```

### Optional Methods

#### cast()

Defines model casting (returns `Output` or `null`).

```php
public function cast(): ?Output
{
    return new Output(
        key: $this->field->name->snakeCase(),
        value: "'string'"
    );
}
```

#### fillable()

Whether the field is mass-assignable (returns `Output` or `null`).

```php
public function fillable(): ?Output
{
    return new Output($this->field->name->snakeCase());
}
```

#### resourceField()

How the field appears in API resources (returns `Output` or `null`).

```php
public function resourceField(): ?Output
{
    return new Output(
        key: $this->field->name->snakeCase(),
        value: "\$this->{$this->field->name->snakeCase()}"
    );
}
```

#### relation()

Defines relationship methods (returns `Output` or `null`).

```php
public function relation(): ?Output
{
    return new Output(
        "public function {$this->field->name->camelCase()}()
        {
            return \$this->belongsTo(Category::class);
        }"
    );
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

Creates basic validation rules:

```php
use HasSimpleRule;

public function rule(): Output
{
    return $this->buildSimpleRule('string|max:255');
    // Produces: 'field_name' => ['required', 'string', 'max:255']
}
```

### IsFillable

Makes the field mass-assignable:

```php
use IsFillable;

// Automatically implements fillable() to return the field name
```

### IsSimpleResourceField

Adds field to API resources:

```php
use IsSimpleResourceField;

// Automatically implements resourceField() to return the field
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

## Advanced Example: Phone Number Type

Here's a complete example of a custom phone number field type:

```php
<?php

namespace App\FieldTypes;

use JCSoriano\LaravelCrudTemplates\DataObjects\Output;
use JCSoriano\LaravelCrudTemplates\FieldTypes\FieldType;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\IsFillable;
use JCSoriano\LaravelCrudTemplates\FieldTypes\Traits\IsSimpleResourceField;

class PhoneType extends FieldType
{
    use IsFillable;
    use IsSimpleResourceField;

    public function migration(): Output
    {
        $field = $this->field;
        $nullable = $field->required ? '' : '->nullable()';
        
        return new Output(
            "\$table->string('{$field->name->snakeCase()}', 20){$nullable};"
        );
    }

    public function rule(): Output
    {
        $required = $this->field->required ? 'required' : 'nullable';
        
        return new Output(
            key: $this->field->name->snakeCase(),
            value: "{$required}|string|regex:/^[+]?[0-9]{10,15}\$/"
        );
    }

    public function factory(): Output
    {
        return new Output("fake()->regexify('[+][0-9]{11}')");
    }
}
```

Register it in your service provider's `register()` method:

```php
$this->app->bind('laravel-crud-templates::field-type::phone', PhoneType::class);
```

Use it:

```bash
php artisan crud:generate Contact --fields="name:string,phone:phone,mobile?:phone"
```

## Overriding Existing Field Types

You can override existing field types by binding to the same container key in your service provider's `register()` method:

```php
use App\FieldTypes\CustomStringType;

public function register()
{
    $this->app->bind('laravel-crud-templates::field-type::string', CustomStringType::class);
}
```

Now all `string` fields will use your custom implementation.

## The Field Object

Inside your field type, you have access to `$this->field`, which contains:

```php
$this->field->name;       // Name object with various case formats
$this->field->required;   // Boolean: is field required?
$this->field->type;       // String: the field type name
$this->field->options;    // Array: additional options (e.g., enum class)
```

### Name Object Methods

```php
$this->field->name->name;         // Original name
$this->field->name->snakeCase();  // snake_case
$this->field->name->camelCase();  // camelCase
$this->field->name->studlyCase(); // StudlyCase
$this->field->name->plural();     // pluralized form
```

## Next Steps

- Learn about [Customizing Generators](/templates/customizing-generators) to modify generation logic
- Explore [Creating Your Own Template](/templates/custom) for different CRUD patterns
- Check the package source code for more field type examples
