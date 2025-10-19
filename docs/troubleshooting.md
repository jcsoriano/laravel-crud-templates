# Troubleshooting

This guide covers common issues you might encounter when using CRUD Templates for Laravel and how to resolve them.

## Common Errors

### Invalid Fields Exception

**Error Message:**
```
Invalid fields: Field 'status' has invalid type 'unknown-type'
```

**Cause:**
You specified a field type that doesn't exist or isn't registered.

**Solution:**
1. Check for typos in your field type
2. Verify you're using a supported field type (see [Field Types](/guide/field-types))
3. If using a custom field type, ensure it's properly registered:

```php
use JCSoriano\LaravelCrudTemplates\Facades\LaravelCrudTemplates;

LaravelCrudTemplates::registerFieldType('your-type', YourFieldType::class);
```

---

### Database Connection Issues

**Error Message:**
```
SQLSTATE[HY000] [2002] Connection refused
```

**Cause:**
The package can't connect to your database when using generate from table.

**Solution:**
1. Verify your database connection in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

2. Test your connection:
```bash
php artisan db:show
```

3. Ensure your database server is running

4. If using generate from table, make sure the table exists:
```bash
php artisan db:table posts
```

---

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

### Namespace Issues

**Error Message:**
```
Class 'App\Models\Content\Post' not found
```

**Cause:**
Namespaced models aren't being autoloaded properly.

**Solution:**
1. Run composer autoload dump:
```bash
composer dump-autoload
```

2. Verify the namespace in the generated model matches the directory structure:
   - Model: `App\Models\Content\Post`
   - File: `app/Models/Content/Post.php`

3. Check your `composer.json` has the correct PSR-4 autoloading:
```json
"autoload": {
    "psr-4": {
        "App\\": "app/"
    }
}
```

---

### File Already Exists

**Error Message:**
```
File already exists: app/Models/Post.php
```

**Cause:**
The generator is trying to create a file that already exists.

**Solution:**

**Option 1: Delete existing files**
```bash
rm app/Models/Post.php
# Then run the command again
```

**Option 2: Rename existing files**
```bash
mv app/Models/Post.php app/Models/Post.php.backup
```

**Option 3: Generate with a different name**
```bash
php artisan crud:generate PostV2 --fields="title:string"
```

::: warning
The package doesn't merge with existing files. If you need to regenerate, backup and delete the existing files first.
:::

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
