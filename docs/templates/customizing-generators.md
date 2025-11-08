# Customizing Generators

Generators are responsible for creating individual files during CRUD generation, or running commands as part of the pipeline.

You can [Create a Generator](#creating-a-generator), or you can use (or extend) the [Available Generators](#available-generators).

## Creating a Generator

There are two types of generators:

1. The **Standard Generator**, which generates a single file from a stub, replacing placeholders with actual content.

2. The **Custom Generator**, which doesn't follow the standard pattern, and can be used for complex logic like running commands or modifying existing files.

### Quick Start: Using the Make Command

The fastest way to create a new generator is using the `make:generator` command:

```bash
# Create a standard generator (recommended for most cases)
php artisan make:generator Repository

# Or create a custom generator for complex logic
php artisan make:generator Repository --custom
```

This command will:
1. Create a generator class at `app/Generators/{Name}Generator.php`
2. Automatically register it in `app/Providers/CrudTemplatesServiceProvider.php`

::: tip
Use `--force` to overwrite an existing generator file.
:::

### Creating a Standard Generator

#### Step 1: Create a stub file

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

#### Step 2: Run the `make` command

Run the `make:generator` command to create a new generator:

```bash
php artisan make:generator Repository --standard
```

This will generate the following file:

```php
<?php

namespace App\Generators;

use JCSoriano\CrudTemplates\Generators\Generator;
use JCSoriano\CrudTemplates\Generators\StandardGenerator;
use JCSoriano\CrudTemplates\DataObjects\Payload;

class RepositoryGenerator extends Generator
{
    use StandardGenerator;

    /**
     * Returns the directory path where the file will be created.
     */
    protected function directory(Payload $payload): string
    {
        return app_path('path/to/directory');
    }

    /**
     * Returns the file name without the `.php` extension.
     */
    protected function fileName(Payload $payload): string
    {
        return $payload->model->model()->studlyCase();
    }

    /**
     * Returns the file type used in console logging (e.g., "Controller", "Model", "Policy").
     */
    protected function fileType(Payload $payload): string
    {
        return 'File';
    }

    /**
     * Returns the path to the stub file (checks custom stubs directory first).
     */
    protected function stubPath(Payload $payload): string
    {
        return 'your-stub-file.stub';
    }
}
```

Simply fill out the methods with the appropriate values.

```php
<?php

namespace App\Generators;

use JCSoriano\CrudTemplates\Generators\Generator;
use JCSoriano\CrudTemplates\Generators\StandardGenerator;
use JCSoriano\CrudTemplates\DataObjects\Payload;

class RepositoryGenerator extends Generator
{
    use StandardGenerator;

    protected function directory(Payload $payload): string
    {
        $namespace = $payload->model->namespace();
        
        return app_path('Repositories/'.str_replace('\\', '/', $namespace));
    }

    protected function fileName(Payload $payload): string
    {
        return $payload->model->model()->studlyCase().'Repository';
    }

    protected function fileType(Payload $payload): string
    {
        return 'Repository';
    }

    protected function stubPath(Payload $payload): string
    {
        return 'repository.stub';
    }

    protected function variables(Payload $payload): array
    {
        $modelName = $payload->model->model()->studlyCase();
        $modelNamespace = $this->buildNamespace('App\\Models', $payload);
        
        $namespaces = collect([
            "{$modelNamespace}\\{$modelName}",
            'Illuminate\Support\Collection',
        ]);

        return [
            ...$payload->variables(),
            'NAMESPACES' => $this->buildNamespaces($namespaces),
        ];
    }
}
```

The `StandardGenerator` trait automatically handles:
- Creating the directory if it doesn't exist
- Checking if the file exists (respects `--force` flag)
- Generating the file from the stub
- Logging the generated file
- Returning the payload

The `make` command will also automatically register the generator in the `CrudTemplatesServiceProvider`:

```php
use App\Generators\RepositoryGenerator;

public function registerGenerators(): void
{
    $this->app->bind('crud-templates::generator::repository', RepositoryGenerator::class);
}
```

You can then now use it in your custom template:

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

### Creating a Custom Generator

You can also create a custom generator for complex logic like running commands or modifying existing files.

#### Step 1: Run the `make` command

Run the `make:generator` command to create a new generator:

```bash
php artisan make:generator RunTests --custom
```

This will generate the following file:

```php
<?php

namespace {{ namespace }};

use JCSoriano\CrudTemplates\Generators\Generator;
use JCSoriano\CrudTemplates\DataObjects\Payload;

class RunTestsGenerator extends Generator
{
    /**
     * Generate the file.
     */
    public function generate(Payload $payload): Payload
    {
        // Implement your custom generation logic here

        return $payload;
    }
}
```

Simply fill out the `generate()` method with the appropriate logic.

```php
<?php

namespace App\Generators;

use JCSoriano\CrudTemplates\Generators\Generator;
use JCSoriano\CrudTemplates\DataObjects\Payload;
use Illuminate\Support\Facades\Process;

class RunTestsGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $namespace = $model->namespace();
        $modelName = $model->model()->studlyCase();
        
        // Build path to the test file
        $directory = base_path('tests/Feature/Api/'.str_replace('\\', '/', $namespace));
        $fileName = $modelName.'ControllerTest';
        $testPath = $directory.'/'.$fileName.'.php';
        
        $result = Process::run("php artisan test {$testPath}");
        
        // Output test results to console
        $payload->components->line($result->output());
        
        return $payload;
    }
}
```

The `make` command will automatically register the generator in the `CrudTemplatesServiceProvider`:

```php
use App\Generators\RunTestsGenerator;

public function registerGenerators(): void
{
    $this->app->bind('crud-templates::generator::run-tests', RunTestsGenerator::class);
}
```

Use this approach when you need to:
- Run commands or external tools
- Modify existing files instead of creating new ones
- Perform operations on files created earlier in the pipeline
- Execute multiple operations in sequence
- Implement complex logic that doesn't fit the standard pattern

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

- **Stub File**: `api/controller.stub`
- **Generates file**: `app/Http/Controllers/Api/Content/PostController.php`

### model

- **Stub File**: `api/model.stub`
- **Generates file**: `app/Models/Content/Post.php`

### policy

- **Stub File**: `api/policy.stub`
- **Generates file**: `app/Policies/PostPolicy.php`

### store-request

- **Stub File**: `api/request.store.stub`
- **Generates file**: `app/Http/Requests/Content/StorePostRequest.php`

### update-request

- **Stub File**: `api/request.update.stub`
- **Generates file**: `app/Http/Requests/Content/UpdatePostRequest.php`

### resource

- **Stub File**: `api/resource.stub`
- **Generates file**: `app/Http/Resources/Content/PostResource.php`

### migration

- **Stub File**: `api/migration.stub`
- **Generates file**: `database/migrations/2024_01_01_000000_create_posts_table.php`

### factory

- **Stub File**: `api/factory.stub`
- **Generates file**: `database/factories/Content/PostFactory.php`

### test

- **Stub File**: `api/test.stub`
- **Generates file**: `tests/Feature/Api/Content/PostControllerTest.php`

### api-route

- **Stub File**: `api/route.stub`
- **Generates file**: Appends routes to `routes/api.php`

### pint

- **Stub File**: N/A
- **Generates file**: Runs Laravel Pint on generated files

## Customizing Stubs

All stubs are completely customizable. You can publish the stubs for modification by running:

```bash
php artisan vendor:publish --tag="crud-templates-stubs"
```

See [Customizing Stubs](/templates/customizing-stubs) for more details.

## Overriding existing generators

To override an existing generator, you can bind your custom generator to the key of the generator you want to override (e.g., `crud-templates::generator::controller`).

```php
use App\Generators\MyCustomControllerGenerator;

public function registerGenerators(): void
{
    $this->app->bind('crud-templates::generator::controller', MyCustomControllerGenerator::class);
}
```

Now, any template that has the `controller` generator will use your custom generator instead.

## StandardGenerator Trait Reference

The `StandardGenerator` trait simplifies generator creation by handling the common pattern of generating a single file from a stub.

### Required Abstract Methods

When using `StandardGenerator`, you must implement these four abstract methods:

#### directory(Payload $payload): string

Returns the directory path where the file will be created.

```php
protected function directory(Payload $payload): string
{
    $namespace = $payload->model->namespace();
    
    return app_path('Http/Controllers/Api/'.str_replace('\\', '/', $namespace));
}
```

#### fileName(Payload $payload): string

Returns the file name without the `.php` extension.

```php
protected function fileName(Payload $payload): string
{
    return $payload->model->model()->studlyCase().'Controller';
}
```

#### fileType(Payload $payload): string

Returns the file type used in console logging (e.g., "Controller", "Model", "Policy").

```php
protected function fileType(Payload $payload): string
{
    return 'Controller';
}
```

#### stubPath(Payload $payload): string

Returns the path to the stub file (checks custom stubs directory first).

```php
protected function stubPath(Payload $payload): string
{
    return 'api/controller.stub';
}
```

### Overridable Methods

These methods have default implementations but can be overridden:

#### variables(Payload $payload): array

Returns variables for stub placeholder replacement. By default, returns `$payload->variables()`.

```php
protected function variables(Payload $payload): array
{
    $modelName = $payload->model->model()->studlyCase();
    
    return [
        ...$payload->variables(),
        'CUSTOM_VAR' => 'custom value',
    ];
}
```

#### conditions(Payload $payload): array

Returns conditions for stub conditional logic. By default, returns `$payload->conditions()`.

```php
protected function conditions(Payload $payload): array
{
    return $payload->conditions();
}
```

#### shouldSkipGeneration(Payload $payload): bool

Returns whether to skip generation. By default, returns `false`.

```php
protected function shouldSkipGeneration(Payload $payload): bool
{
    if ($payload->table) {
        $payload->components->warn('Skipping generation...');

        return true;
    }
    
    return false;
}
```

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
public function variables(Payload $payload): Payload
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

See [Customizing Printers](/templates/customizing-printers) for more details.

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

