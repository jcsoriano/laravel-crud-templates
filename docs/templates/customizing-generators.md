# Customizing Generators

Generators are responsible for creating individual files during CRUD generation. 

It takes a stub file and generates a new file based on it, replacing placeholders with the actual content. 

## Available Generators

Below is a list of available generators, the stub they are based on, and the file they generate:

- [controller](#controller)
- [model](#model)
- [policy](#policy)
- [store-request](#store-request)
- [update-request](#update-request)
- [resource](#resource)
- [migration](#migration)
- [factory](#factory)
- [test](#test)
- [api-route](#api-route)
- [pint](#pint)

### controller

- **Stub File**: `api.controller.stub`
- **Generates file**: `app/Http/Controllers/Api/Content/PostController.php`

### model

- **Stub File**: `api.model.stub`
- **Generates file**: `app/Models/Content/Post.php`

### policy

- **Stub File**: `api.policy.stub`
- **Generates file**: `app/Policies/PostPolicy.php`

### store-request

- **Stub File**: `api.request.store.stub`
- **Generates file**: `app/Http/Requests/Content/StorePostRequest.php`

### update-request

- **Stub File**: `api.request.update.stub`
- **Generates file**: `app/Http/Requests/Content/UpdatePostRequest.php`

### resource

- **Stub File**: `api.resource.stub`
- **Generates file**: `app/Http/Resources/Content/PostResource.php`

### migration

- **Stub File**: `api.migration.stub`
- **Generates file**: `database/migrations/2024_01_01_000000_create_posts_table.php`

### factory

- **Stub File**: `api.factory.stub`
- **Generates file**: `database/factories/Content/PostFactory.php`

### test

- **Stub File**: `api.test.stub`
- **Generates file**: `tests/Feature/Api/Content/PostControllerTest.php`

### api-route

- **Stub File**: N/A
- **Generates file**: Appends routes to `routes/api.php`

### pint

- **Stub File**: N/A
- **Generates file**: Runs Laravel Pint on generated files

## Customizing Stubs

All stubs are completely customizable. You can publish the stubs for modification by running:

```bash
php artisan vendor:publish --tag="laravel-crud-templates-stubs"
```

See [Customizing Stubs](/templates/customizing-stubs) for more details.

## Creating a Custom Generator

Aside from the built-in generators, you can create your own custom generators. In the example below, we'll create a custom generator for the repository pattern.

### Step 1: Create the Stub File

First, create the stub file that defines what you want to generate. Create the stub file at `stubs/crud.repository.stub`:

::: v-pre
```php
<?php

namespace App\Repositories{{ NAMESPACE_PATH }};

{{ NAMESPACES }}

class {{ MODEL }}Repository
{
    public function all(): Collection
    {
        return {{ MODEL }}::all();
    }
    
    public function find(int $id): ?{{ MODEL }}
    {
        return {{ MODEL }}::find($id);
    }
    
    public function create(array $data): {{ MODEL }}
    {
        return {{ MODEL }}::create($data);
    }
    
    public function update({{ MODEL }} ${{ MODEL_CAMEL }}, array $data): {{ MODEL }}
    {
        ${{ MODEL_CAMEL }}->update($data);
        
        return ${{ MODEL_CAMEL }};
    }
    
    public function delete({{ MODEL }} ${{ MODEL_CAMEL }}): bool
    {
        return ${{ MODEL_CAMEL }}->delete();
    }
}
```
:::

### Step 2: Create the Generator Class

Create a class that extends `Generator`:

```php
<?php

namespace App\Generators;

use JCSoriano\LaravelCrudTemplates\Generators\Generator;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

class RepositoryGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $namespace = $model->namespace();
        $modelName = $model->model()->studlyCase();
        
        // Create repositories directory
        $directory = app_path('Repositories/'.str_replace('\\', '/', $namespace));
        $this->createDirectoryIfNotExists($directory);
        
        $fileName = $modelName.'Repository';
        
        // Check if file exists and return early if not forcing
        if ($this->checkIfFileExists('Repository', $directory, $fileName, $payload)) {
            return $payload;
        }
        
        // Build namespace paths
        $modelNamespace = $this->buildNamespace('App\\Models', $payload);
        
        $namespaces = collect([
            "{$modelNamespace}\\{$modelName}",
            'Illuminate\Support\Collection',
        ]);
        
        // Generate from custom stub
        $this->generateFile(
            stubPath: 'crud.repository.stub',
            directory: $directory,
            fileName: $fileName,
            variables: [
                ...$payload->variables(),
                'NAMESPACES' => $this->buildNamespaces($namespaces),
            ],
            conditions: $payload->conditions(),
        );
        
        $this->logGeneratedFile('Repository', $directory, $fileName, $payload);
        
        return $payload;
    }
}
```

### Step 3: Register the Generator

Register your generator in a service provider's `register()` method:

```php
use App\Generators\RepositoryGenerator;

public function register()
{
    $this->app->bind('laravel-crud-templates::generator::repository', RepositoryGenerator::class);
}
```

**Note:** To override an existing generator, bind to the same key (e.g., `laravel-crud-templates::generator::controller`). To create a new generator, choose a unique key or use the class directly in your template without binding (see Step 4).

### Step 4: Use the Generator in a Template

Add your generator to your template. You can use either the registered key or the class name directly:

```php
use App\Generators\RepositoryGenerator;

protected function template(): array
{
    return $this->buildGenerators([
        'controller',
        'model',
        'repository', // Your registered generator
        'migration',
        // ... other generators
    ]);
}
```

## Generator Lifecycle

Generators are executed as a pipeline:

1. Template defines which generators to use
2. Each generator receives a `Payload` object
3. Generator processes the payload and generates file(s)
4. Generator returns the modified payload
5. Next generator in the pipeline receives the payload

## The Payload Object

The `Payload` object contains all data needed for generation:

### Properties

```php
// Core properties
$payload->model;        // Model: Model data (name, namespace, etc.)
$payload->fields;       // Collection: Field definitions
$payload->components;   // Factory: Laravel console components for output (read-only)
$payload->force;        // bool: Force overwrite existing files (read-only)
$payload->table;        // ?string: Database table name (read-only)

// Configuration arrays
$payload->options;      // array: Additional options passed to the command
$payload->variables;    // array: Custom variables for stub replacement
$payload->conditions;   // array: Custom conditions for stub logic
$payload->data;         // array: Additional data for generators
$payload->skip;         // array: Generators to skip
```

### Methods

```php
$payload->variables();  // Returns array: All variables for stub replacement (includes MODEL, MODEL_CAMEL, NAMESPACE_PATH, etc.)
$payload->conditions(); // Returns array: All conditions for stub logic (includes scopeUser, scopeTeam, scopeNone)
```

**Note:** Properties marked as "read-only" can be accessed but not modified directly. The `variables` and `conditions` properties are arrays you can modify to add custom values, while the `variables()` and `conditions()` methods return the complete merged arrays including defaults.

## Helper Methods

The `Generator` base class provides useful helper methods:

### createDirectoryIfNotExists()

Creates a directory if it doesn't exist:

```php
$this->createDirectoryIfNotExists(app_path('Http/Controllers/Api'));
```

### getStubPath()

Gets the path to a stub file (checks custom stubs first):

```php
$stubPath = $this->getStubPath('crud.controller.stub');
```

### logGeneratedFile()

Logs a generated file to the console:

```php
$this->logGeneratedFile('Controller', $directory, $fileName, $payload);
```

### buildNamespace()

Builds a namespace with the model's namespace path:

```php
$namespace = $this->buildNamespace('App\\Models', $payload);
// Returns: App\Models\Content (if model is Content/Post)
```

### buildNamespaces()

Builds multiple use statements from a collection:

```php
$namespaces = collect([
    'App\Models\Post',
    'Illuminate\Http\Request',
]);

$useStatements = $this->buildNamespaces($namespaces);
// Returns: "use App\Models\Post;\nuse Illuminate\Http\Request;"
```

## Working with Printers

Printers generate specific parts of files (like fillable arrays, casts, etc.). You can use printers in your generators:

```php
public function generate(Payload $payload): Payload
{
    // Use a printer (inherited from Generator base class)
    $fillableOutput = $this->print('fillable', $payload);
    $fillableFields = $fillableOutput->output;
    
    // Add to variables
    $variables = [
        ...$payload->variables(),
        'FILLABLE' => $fillableFields,
    ];
    
    // ... rest of generation logic
}
```

Available printers (used via the `print()` method):
- `casts` - Model type casts
- `factory` - Factory field definitions
- `fillable` - Fillable array
- `migrations` - Migration columns
- `relations` - Relationship methods
- `resource-only` - Resource fields (non-relations)
- `resource-relation` - Resource relationship fields
- `rules` - Validation rules
- `dbAssertions` - Database assertion columns

## Accessing Field Data

Process fields in your generator:

```php
public function generate(Payload $payload): Payload
{
    $fields = $payload->fields;
    
    // Filter specific field types
    $relationFields = $fields->filter(function ($field) {
        return in_array($field->type->name, ['belongsTo', 'hasMany', 'belongsToMany']);
    });
    
    // Check if certain fields exist
    $hasTimestamps = $fields->contains(function ($field) {
        return $field->name->name === 'created_at';
    });
    
    // Add to payload data
    $payload->data['has_relations'] = $relationFields->isNotEmpty();
    
    return $payload;
}
```

## Next Steps

- Explore [Customizing Stubs](/templates/customizing-stubs) to change generated code
- Check [Customizing Field Types](/templates/customizing-field-types) for custom field logic
- Learn about [Customizing Printers](/templates/customizing-printers) to modify code snippets

