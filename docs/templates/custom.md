# Creating Your Own Template

The ability to create your own template is one of the most powerful features of CRUD Templates for Laravel. It allows you to generate different types of CRUDs beyond the default API pattern.

## Basic Template Structure

At its simplest, a template just defines which generators to use. See [Available Generators](/templates/customizing-generators#available-generators) for a list of built-in generators. You can also [Create Custom Generators](/templates/customizing-generators#creating-a-custom-generator).

```php
<?php

namespace App\Templates;

use JCSoriano\CrudTemplates\Templates\Template;

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

### Register the Template

After creating your template, register it in a service provider's `register()` method:

```php
use App\Templates\MyCustomTemplate;

public function register()
{
    $this->app->bind('laravel-crud-templates::template::my-custom', MyCustomTemplate::class);
}
```

::: tip
To override an existing template (like 'api'), bind to the same key: `laravel-crud-templates::template::api`.
:::

### Use the Template

Voilà! You can now use your custom template:

```bash
php artisan crud:generate Post --template=my-custom --fields="title:string,content:text"
```

## Advanced

Below are some other methods you can use when creating your own template.

### variables()

You can provide custom variables for stub replacement:

```php
protected function variables(): array
{
    return [
        'CUSTOM_VAR' => 'custom value',
        'VERSION' => '1.0.0',
    ];
}
```

::: v-pre
These variables will be available in all stubs as `{{ CUSTOM_VAR }}`.
:::

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

::: v-pre
```php
{{ if useCustomFeature }}
    // Custom feature code
{{ endif }}
```
:::

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

## Next Steps

- Learn about [Customizing Generators](/templates/customizing-generators) to create custom file generators
- Explore [Customizing Stubs](/templates/customizing-stubs) to modify generated code
- Check [Customizing Field Types](/templates/customizing-field-types) for custom field behavior
- Review [Customizing Printers](/templates/customizing-printers) to modify code snippets

