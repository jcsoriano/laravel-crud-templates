# Customizing Generators

Generators are responsible for creating individual files during CRUD generation. Each generator handles one type of file (controller, model, migration, etc.). You can override existing generators or create new ones to customize the generation process.

## Understanding Generators

CRUD Templates for Laravel includes these built-in generators:

- `controller` - API controllers
- `model` - Eloquent models
- `policy` - Authorization policies
- `store-request` - Store validation requests
- `update-request` - Update validation requests
- `resource` - API resources
- `migration` - Database migrations
- `factory` - Model factories
- `test` - Feature tests
- `api-route` - API route registration
- `pint` - Code formatting

## Generator Lifecycle

Generators are executed as a pipeline:

1. Template defines which generators to use
2. Each generator receives a `Payload` object
3. Generator processes the payload and generates file(s)
4. Generator returns the modified payload
5. Next generator in the pipeline receives the payload

## Creating a Custom Generator

### Step 1: Create the Generator Class

Create a class that extends `Generator`:

```php
<?php

namespace App\Generators;

use JCSoriano\LaravelCrudTemplates\Generators\Generator;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;
use JCSoriano\LaravelCrudTemplates\Facades\LaravelStub;

class CustomControllerGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $modelName = $model->model()->studlyCase();
        
        // Define output directory
        $directory = app_path('Http/Controllers/Api');
        $this->createDirectoryIfNotExists($directory);
        
        $fileName = $modelName.'Controller';
        
        // Generate from stub
        LaravelStub::from($this->getStubPath('crud.controller.stub'))
            ->to($directory)
            ->name($fileName)
            ->ext('php')
            ->replaces([
                ...$payload->variables(),
                'CUSTOM_VAR' => 'custom value',
            ])
            ->conditions($payload->conditions())
            ->generate();
        
        // Log the generated file
        $this->logGeneratedFile('Controller', $directory, $fileName, $payload);
        
        return $payload;
    }
}
```

### Step 2: Register the Generator

Register your generator in a service provider's `register()` method:

```php
use App\Generators\CustomControllerGenerator;

public function register()
{
    // Override existing controller generator
    $this->app->bind('laravel-crud-templates::generator::controller', CustomControllerGenerator::class);
    
    // Or register a new generator
    $this->app->bind('laravel-crud-templates::generator::custom-type', CustomControllerGenerator::class);
}
```

**Note:** To override an existing generator, bind to the same key. To create a new generator that doesn't override a package default, you can either register it with a binding or use it directly in your template without binding (see Step 3).

### Step 3: Use the Generator in a Template

If you created a new generator, add it to your template. You can use either the registered key or the class name directly:

```php
use App\Generators\CustomControllerGenerator;

protected function template(): array
{
    return $this->buildGenerators([
        'controller',
        'model',
        'custom-type', // Your registered generator (if you bound it)
        CustomControllerGenerator::class, // Or use directly without binding
        'migration',
        // ... other generators
    ]);
}
```

**Tip:** If you're not overriding an existing generator, you can use the class name directly without registering a binding.

## The Payload Object

The `Payload` object contains all data needed for generation:

```php
$payload->model;        // Model data (name, namespace, etc.)
$payload->fields;       // Collection of field definitions
$payload->components;   // Laravel console components for output
$payload->options;      // Additional options passed to the command
$payload->variables();  // Array of variables for stub replacement
$payload->conditions(); // Array of conditions for stub logic
$payload->data;         // Additional data array
```

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

## Advanced Example: Service Generator

Create a generator that creates service classes:

```php
<?php

namespace App\Generators;

use JCSoriano\LaravelCrudTemplates\Generators\Generator;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;
use JCSoriano\LaravelCrudTemplates\Facades\LaravelStub;

class ServiceGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $namespace = $model->namespace();
        $modelName = $model->model()->studlyCase();
        
        // Create services directory
        $directory = app_path('Services/'.str_replace('\\', '/', $namespace));
        $this->createDirectoryIfNotExists($directory);
        
        $fileName = $modelName.'Service';
        
        // Build namespace paths
        $modelNamespace = $this->buildNamespace('App\\Models', $payload);
        
        $namespaces = collect([
            "{$modelNamespace}\\{$modelName}",
            'Illuminate\Support\Collection',
        ]);
        
        // Custom variables for the stub
        $variables = [
            ...$payload->variables(),
            'NAMESPACES' => $this->buildNamespaces($namespaces),
        ];
        
        // Generate from custom stub
        LaravelStub::from($this->getStubPath('crud.service.stub'))
            ->to($directory)
            ->name($fileName)
            ->ext('php')
            ->replaces($variables)
            ->conditions($payload->conditions())
            ->generate();
        
        $this->logGeneratedFile('Service', $directory, $fileName, $payload);
        
        return $payload;
    }
}
```

Create the stub file at `stubs/crud.service.stub`:

```php
<?php

namespace App\Services{{ NAMESPACE_PATH }};

{{ NAMESPACES }}

class {{ MODEL }}Service
{
    public function getAll(): Collection
    {
        return {{ MODEL }}::all();
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

Register and use it:

```php
// In your AppServiceProvider's register() method
$this->app->bind('laravel-crud-templates::generator::service', ServiceGenerator::class);

// Create custom template that uses it
use App\Generators\ServiceGenerator;

class CustomApiTemplate extends Template
{
    public function template(): array
    {
        return $this->buildGenerators([
            'controller',
            'service',  // Your registered generator
            // Or use directly: ServiceGenerator::class
            'model',
            'migration',
            // ...
        ]);
    }
}
```

## Overriding Existing Generators

### Example: Add Logging to Controller Generator

```php
<?php

namespace App\Generators;

use JCSoriano\LaravelCrudTemplates\Generators\ControllerGenerator as BaseControllerGenerator;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;

class CustomControllerGenerator extends BaseControllerGenerator
{
    public function generate(Payload $payload): Payload
    {
        // Add custom logic before generation
        $payload->data['add_logging'] = true;
        
        // Set custom condition
        $payload->conditions['useLogging'] = true;
        
        // Call parent generation
        $result = parent::generate($payload);
        
        // Add custom logic after generation
        $this->addLoggingTrait($payload);
        
        return $result;
    }
    
    protected function addLoggingTrait(Payload $payload): void
    {
        // Additional custom logic
    }
}
```

Register it in your service provider's `register()` method:

```php
$this->app->bind('laravel-crud-templates::generator::controller', CustomControllerGenerator::class);
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

## Conditional Generation

You can conditionally generate files based on options:

```php
public function generate(Payload $payload): Payload
{
    // Only generate if option is set
    if ($payload->options['with-service'] ?? false) {
        // Generate service file
        $this->generateService($payload);
    }
    
    return $payload;
}
```

Use it:

```bash
php artisan crud:generate Post --fields="title:string" --options="with-service:true"
```

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

- Learn about [Creating Your Own Template](/templates/custom) to combine generators
- Explore [Customizing Stubs](/templates/customizing-stubs) to change generated code
- Check [Customizing Field Types](/templates/customizing-field-types) for custom field logic

