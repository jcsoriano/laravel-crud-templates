# Troubleshooting

This guide covers common issues you might encounter when using CRUD Templates for Laravel and how to resolve them.

## Common Errors

### Table Not Found

**Error Message:**
```
Table 'database.posts' doesn't exist
```

**Cause:**
You're using `--table=posts` but the table doesn't exist in your database.

**Solution:**
1. Check the table name is correct
2. Verify the table exists:
```bash
php artisan db:table posts
```

3. If the table doesn't exist, use `--fields` instead of `--table`

---

### Enum Class Not Found

**Error Message:**
```
Class 'App\Enums\OrderStatus' not found
```

**Cause:**
You specified an enum field type with a class that doesn't exist.

**Solution:**
1. Create the enum class before generating the CRUD:

```php
<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
```

2. Then run the generation:
```bash
php artisan crud:generate Order --fields="status:enum:OrderStatus"
```

---

### Unresolvable Dependency Error

**Error Message:**
```
Unresolvable dependency resolving [Parameter #0 [ <required> string $name ]] 
in class JCSoriano\CrudTemplates\DataObjects\Name
```

**Cause:**
This error can occur if you're binding field types or templates incorrectly in your service provider, causing Laravel to try to instantiate Data Transfer Objects (DTOs) with missing constructor parameters.

**Solution:**
Ensure you're binding field types and templates correctly. The package expects class name strings, not instantiated objects.

**Correct binding:**
```php
public function register()
{
    // Field types - bind to class name
    $this->app->bind('laravel-crud-templates::field-type::custom', CustomFieldType::class);
    
    // Templates - bind to class name
    $this->app->bind('laravel-crud-templates::template::custom', CustomTemplate::class);
}
```

**Incorrect binding (don't do this):**
```php
public function register()
{
    // ❌ Don't try to instantiate with dependencies
    $this->app->bind('laravel-crud-templates::field-type::custom', function () {
        return new CustomFieldType(new Field(...)); // Wrong!
    });
}
```

**Note:** Field types are instantiated internally by the parsers when they have all the required data. You should only bind the class name.
