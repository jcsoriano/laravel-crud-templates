# Creating Your Own Template

Templates define which generators are executed and in what order when you run the CRUD generation command. Creating custom templates allows you to generate different types of CRUDs beyond the default API pattern.

## Template Architecture

A template is a class that:

1. Extends the `Template` base class
2. Defines which generators to use
3. Optionally provides custom variables and conditions
4. Orchestrates the generation pipeline

## Basic Template Structure

Here's the minimal structure of a template:

```php
<?php

namespace App\Templates;

use JCSoriano\LaravelCrudTemplates\Templates\Template;

class MyCustomTemplate extends Template
{
    public function template(): array
    {
        return $this->buildGenerators([
            'controller',
            'model',
            'migration',
        ]);
    }
}
```

## Creating a Custom Template

### Step 1: Create the Template Class

Create a new template class:

```php
<?php

namespace App\Templates;

use JCSoriano\LaravelCrudTemplates\Templates\Template;

class WebTemplate extends Template
{
    public function template(): array
    {
        return $this->buildGenerators([
            'web-controller',  // Custom generator for web controllers
            'model',
            'view',           // Custom generator for views
            'form-request',   // Custom generator for form requests
            'migration',
            'factory',
            'test',
            'web-route',      // Custom generator for web routes
            'pint',
        ]);
    }
}
```

### Step 2: Register the Template

Register your template in a service provider's `register()` method:

```php
use App\Templates\WebTemplate;

public function register()
{
    $this->app->bind('laravel-crud-templates::template::web', WebTemplate::class);
}
```

**Note:** To override an existing template (like 'api'), bind to the same key as the package default.

### Step 3: Use Your Template

Now you can use your custom template:

```bash
php artisan crud:generate Post --fields="title:string,content:text" --template=web
```

## Template Methods

### template()

The `template()` method returns an array of generator instances:

```php
public function template(): array
{
    return $this->buildGenerators([
        'controller',
        'model',
        'migration',
    ]);
}
```

### variables()

Provide custom variables for stub replacement:

```php
protected function variables(): array
{
    return [
        'CUSTOM_VAR' => 'custom value',
        'VERSION' => '1.0.0',
    ];
}
```

These variables are available in all stubs as `{{ CUSTOM_VAR }}`.

### conditions()

Define custom conditions for conditional stub logic:

```php
protected function conditions(): array
{
    return [
        'useCustomFeature' => true,
        'enableLogging' => $this->options['logging'] ?? false,
    ];
}
```

Use in stubs:

```php
{{ if useCustomFeature }}
    // Custom feature code
{{ endif }}
```

### data()

Add custom data to the payload:

```php
public function data(): array
{
    return [
        'author' => 'Your Name',
        'generated_at' => now()->toDateTimeString(),
    ];
}
```

## Complete Example: Web CRUD Template

Here's a complete example of a web-based CRUD template:

### 1. Create the Template

```php
<?php

namespace App\Templates;

use JCSoriano\LaravelCrudTemplates\Templates\Template;

class WebTemplate extends Template
{
    public function template(): array
    {
        return $this->buildGenerators([
            'web-controller',
            'model',
            'view-index',
            'view-create',
            'view-edit',
            'view-show',
            'form-request',
            'migration',
            'factory',
            'web-test',
            'web-route',
            'pint',
        ]);
    }

    protected function variables(): array
    {
        return [
            'VIEW_PATH' => strtolower($this->model->model()->studlyCase()),
        ];
    }

    protected function conditions(): array
    {
        return [
            'useBootstrap' => $this->options['css'] === 'bootstrap',
            'useTailwind' => $this->options['css'] === 'tailwind',
        ];
    }

    public function data(): array
    {
        return [
            'css_framework' => $this->options['css'] ?? 'tailwind',
        ];
    }
}
```

### 2. Create Custom Generators

Create generators for web-specific files:

```php
<?php

namespace App\Generators;

use JCSoriano\LaravelCrudTemplates\Generators\Generator;
use JCSoriano\LaravelCrudTemplates\DataObjects\Payload;
use JCSoriano\LaravelCrudTemplates\Facades\LaravelStub;

class WebControllerGenerator extends Generator
{
    public function generate(Payload $payload): Payload
    {
        $model = $payload->model;
        $modelName = $model->model()->studlyCase();
        
        $directory = app_path('Http/Controllers');
        $this->createDirectoryIfNotExists($directory);
        
        $fileName = $modelName.'Controller';
        
        LaravelStub::from($this->getStubPath('web.controller.stub'))
            ->to($directory)
            ->name($fileName)
            ->ext('php')
            ->replaces($payload->variables())
            ->conditions($payload->conditions())
            ->generate();
        
        $this->logGeneratedFile('Web Controller', $directory, $fileName, $payload);
        
        return $payload;
    }
}
```

### 3. Register Everything

Register in your service provider's `register()` method:

```php
use App\Templates\WebTemplate;
use App\Generators\WebControllerGenerator;
use App\Generators\ViewIndexGenerator;
// ... other generators

public function register()
{
    // Register template
    $this->app->bind('laravel-crud-templates::template::web', WebTemplate::class);
    
    // Register generators
    $this->app->bind('laravel-crud-templates::generator::web-controller', WebControllerGenerator::class);
    $this->app->bind('laravel-crud-templates::generator::view-index', ViewIndexGenerator::class);
    // ... register other generators
}
```

Alternatively, you can use custom generators directly in your template without binding them (see below).

### 4. Create Stubs

Create stub files for your generators in `stubs/`:

**web.controller.stub:**
```php
<?php

namespace App\Http\Controllers;

use App\Models\{{ MODEL }};
use Illuminate\Http\Request;

class {{ MODEL }}Controller extends Controller
{
    public function index()
    {
        ${{ MODEL_PLURAL }} = {{ MODEL }}::paginate(15);
        
        return view('{{ VIEW_PATH }}.index', compact('{{ MODEL_PLURAL }}'));
    }
    
    public function create()
    {
        return view('{{ VIEW_PATH }}.create');
    }
    
    public function store(Request $request)
    {
        {{ MODEL }}::create($request->validated());
        
        return redirect()->route('{{ VIEW_PATH }}.index')
            ->with('success', '{{ MODEL }} created successfully.');
    }
    
    // ... other methods
}
```

### 5. Use the Template

```bash
php artisan crud:generate Post \
  --fields="title:string,content:text,published:boolean" \
  --template=web \
  --options="css:tailwind"
```

## Advanced: Conditional Generators

You can conditionally include generators based on options:

```php
public function template(): array
{
    $generators = [
        'controller',
        'model',
        'migration',
    ];
    
    // Add API generators if option is set
    if ($this->options['with-api'] ?? false) {
        $generators[] = 'resource';
        $generators[] = 'api-route';
    }
    
    // Add views if option is set
    if ($this->options['with-views'] ?? false) {
        $generators = array_merge($generators, [
            'view-index',
            'view-create',
            'view-edit',
        ]);
    }
    
    $generators[] = 'pint';
    
    return $this->buildGenerators($generators);
}
```

Use it:

```bash
php artisan crud:generate Post \
  --fields="title:string" \
  --template=custom \
  --options="with-api:true,with-views:true"
```

## Accessing Model and Fields

Access model and field data in your template methods:

```php
protected function variables(): array
{
    $hasRelations = $this->fields->contains(function ($field) {
        return in_array($field->type->name, ['belongsTo', 'hasMany']);
    });
    
    return [
        'HAS_RELATIONS' => $hasRelations,
        'MODEL_LOWER' => strtolower($this->model->model()->studlyCase()),
        'FIELD_COUNT' => $this->fields->count(),
    ];
}
```

## Reusing Existing Generators

You don't have to create all generators from scratch. Mix existing package generators with your custom ones:

```php
use App\Generators\CustomServiceGenerator;

public function template(): array
{
    return $this->buildGenerators([
        'controller',      // Use existing API controller generator
        'model',          // Use existing model generator
        'custom-service', // Your registered custom generator
        CustomServiceGenerator::class, // Or use directly without binding
        'migration',      // Use existing migration generator
        'pint',          // Use existing pint generator
    ]);
}
```

**Tip:** You can use generator class names directly without registering them if you're not overriding package defaults.

## Example Templates

### Minimal Template

For simple models without controllers:

```php
class MinimalTemplate extends Template
{
    public function template(): array
    {
        return $this->buildGenerators([
            'model',
            'migration',
            'factory',
        ]);
    }
}
```

### Full-Stack Template

Complete web and API template:

```php
class FullStackTemplate extends Template
{
    public function template(): array
    {
        return $this->buildGenerators([
            'api-controller',
            'web-controller',
            'model',
            'policy',
            'store-request',
            'update-request',
            'resource',
            'view-index',
            'view-create',
            'view-edit',
            'migration',
            'factory',
            'api-test',
            'web-test',
            'api-route',
            'web-route',
            'pint',
        ]);
    }
}
```

### GraphQL Template

For GraphQL APIs:

```php
class GraphQLTemplate extends Template
{
    public function template(): array
    {
        return $this->buildGenerators([
            'model',
            'graphql-type',
            'graphql-query',
            'graphql-mutation',
            'migration',
            'factory',
            'graphql-test',
            'pint',
        ]);
    }
}
```

## Next Steps

- Review the [API Template](/templates/api) for implementation examples
- Learn about [Customizing Generators](/templates/customizing-generators) to create custom file generators
- Explore [Customizing Stubs](/templates/customizing-stubs) to modify generated code
- Check [Customizing Field Types](/templates/customizing-field-types) for custom field behavior

