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

## Creating a new template

You can easily create a new template using the `make:template` command:

```bash
php artisan make:template MyCustom
```

This command will generate a template class at `app/Templates/{Name}Template.php` with the following content:

```php
<?php

namespace App\Templates;

use JCSoriano\CrudTemplates\Templates\Template;

class MyCustomTemplate extends Template
{
    public function template(): array
    {
        return $this->buildGenerators([
            // List your generators here
        ]);
    }
}
```

::: tip
Use `--force` to overwrite an existing template file.
:::

### Registering the Template

The command also automatically registers the template in the `CrudTemplatesServiceProvider`:

```php
use App\Templates\MyCustomTemplate;

public function registerTemplates(): void
{
    $this->app->bind('crud-templates::template::my-custom', MyCustomTemplate::class);
}
```

::: tip
To override an existing template (like 'api'), bind to the same key: `crud-templates::template::api`.
:::

### Use the Template

Voilà! After setting what generators to use, you can now use your custom template:

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
These variables will replace placeholders in the stubs as `{{ CUSTOM_VAR }}`.
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

Now that you've learned about the API template or created your own template, you can learn about how to use it by feeding fields into the templates:
 - Explore the [Field Types](/guide/field-types) to learn about the available field types or create new ones
 - Explore the [Relationships](/guide/relationships) to learn about how to define and generate model relationships
 - Explore the [Generate from Schema](/guide/generate-from-schema) to learn how to generate CRUD from existing database tables

