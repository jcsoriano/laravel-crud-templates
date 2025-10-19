# Laravel CRUD Templates

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jcsoriano/laravel-crud-templates.svg?style=flat-square)](https://packagist.org/packages/jcsoriano/laravel-crud-templates)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jcsoriano/laravel-crud-templates/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jcsoriano/laravel-crud-templates/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jcsoriano/laravel-crud-templates/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jcsoriano/laravel-crud-templates/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jcsoriano/laravel-crud-templates.svg?style=flat-square)](https://packagist.org/packages/jcsoriano/laravel-crud-templates)

Laravel CRUD Templates is a powerful package that allows you to quickly generate complete CRUD (Create, Read, Update, Delete) operations for your Laravel applications. With a single command, you can generate controllers, models, policies, requests, resources, migrations, factories, and tests.

## Features

- **Complete CRUD Generation**: Generate all necessary files for CRUD operations in one command
- **Flexible Field Types**: Support for various field types including relationships
- **Customizable**: Extend with custom field types, generators, and templates
- **Multiple Templates**: Support for different CRUD patterns (API, Web, etc.)
- **Laravel Standards**: Generated code follows Laravel conventions and best practices
- **Table Introspection**: Can generate fields from existing database tables

## Installation

You can install the package via composer:

```bash
composer require jcsoriano/laravel-crud-templates
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-crud-templates-config"
```

## Usage

### Basic Usage

Generate CRUD files for a simple model:

```bash
php artisan crud:generate Post --fields="title:string,content:text,published:boolean"
```

This will generate:
- `app/Http/Controllers/Api/PostController.php`
- `app/Models/Post.php`
- `app/Policies/PostPolicy.php`
- `app/Http/Requests/StorePostRequest.php`
- `app/Http/Requests/UpdatePostRequest.php`
- `app/Http/Resources/PostResource.php`
- `database/migrations/{timestamp}_create_posts_table.php`
- `database/factories/PostFactory.php`
- `tests/Feature/Api/PostControllerTest.php`

### Nested Models

Generate CRUD files for models with namespaces:

```bash
php artisan crud:generate Content/Post --fields="title:string,body:text"
```

This will create the model in `app/Models/Content/Post.php` and organize all other files accordingly.

### Field Types

The package supports various field types:

```bash
# Basic types
php artisan crud:generate Product --fields="name:string,price:decimal,description:text,active:boolean,created_date:date,updated_at:datetime,metadata:json"

# Nullable fields (add ? after field name)
php artisan crud:generate User --fields="name:string,email:string,phone?:string"

# Enum fields (requires enum class specification)
php artisan crud:generate Order --fields="status:enum:OrderStatus,priority:enum:Priority"

# Relationships
php artisan crud:generate Post --fields="title:string,category:belongsTo,tags:belongsToMany"
```

### Supported Field Types

| Type | Migration | Validation Rule | Model Cast | Example |
|------|-----------|----------------|------------|---------|
| `string` | `->string('field')` | `string\|max:255` | - | `title:string` |
| `integer` | `->integer('field')` | `integer` | - | `count:integer` |
| `decimal` | `->decimal('field', 8, 2)` | `numeric` | `decimal:2` | `price:decimal` |
| `date` | `->date('field')` | `date` | `immutable_date` | `birth_date:date` |
| `datetime` | `->dateTime('field')` | `date` | `immutable_datetime` | `published_at:datetime` |
| `text` | `->text('field')` | `string` | - | `description:text` |
| `boolean` | `->boolean('field')` | `boolean` | `boolean` | `active:boolean` |
| `enum` | `->string('field')` | - | `EnumClass::class` | `status:enum:Status` |
| `json` | `->json('field')` | `array` | `array` | `metadata:json` |
| `belongsTo` | `->foreignId('model_id')->constrained()` | `exists:table,id` | - | `category:belongsTo` |
| `hasMany` | - | - | Relationship method | `posts:hasMany` |
| `belongsToMany` | - | - | Relationship method | `tags:belongsToMany` |
| `morphTo` | `->morphs('field')` | - | Relationship method | `commentable:morphTo` |
| `morphMany` | - | - | Relationship method | `comments:morphMany` |
| `morphToMany` | - | - | Relationship method | `tags:morphToMany` |

### Generate from Existing Table

You can also generate fields from an existing database table:

```bash
php artisan crud:generate Post --table=posts
```

Or combine table introspection with additional fields:

```bash
php artisan crud:generate Post --table=posts --fields="featured:boolean,tags:belongsToMany"
```

### Custom Template Types

By default, the package generates API CRUD files. You can specify different template types:

```bash
php artisan crud:generate Post --fields="title:string" --template=api
```

## Customization

### Custom Field Types

You can register custom field types in your service provider:

```php
use JCSoriano\LaravelCrudTemplates\Facades\LaravelCrudTemplates;

LaravelCrudTemplates::registerFieldType('custom-type', App\FieldTypes\CustomFieldType::class);
```

### Custom Generators

Override existing generators or add new ones:

```php
LaravelCrudTemplates::registerGenerator('controller', App\Generators\CustomControllerGenerator::class);
```

### Custom Templates

Create custom templates for different CRUD patterns:

```php
LaravelCrudTemplates::registerTemplate('web', App\Templates\WebTemplate::class);
```

## Configuration

The config file allows you to customize default behavior:

```php
return [
    'default_template' => 'api',
    
    'field_types' => [
        'custom-type' => App\FieldTypes\CustomType::class,
    ],
    
    'generators' => [
        'controller' => App\Generators\CustomControllerGenerator::class,
    ],
    
    'templates' => [
        'web' => App\Templates\WebTemplate::class,
    ],
];
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [JC Soriano](https://github.com/jcsoriano)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.