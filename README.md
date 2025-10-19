# Laravel CRUD Templates

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jcsoriano/laravel-crud-templates.svg?style=flat-square)](https://packagist.org/packages/jcsoriano/laravel-crud-templates)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/jcsoriano/laravel-crud-templates/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/jcsoriano/laravel-crud-templates/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/jcsoriano/laravel-crud-templates/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/jcsoriano/laravel-crud-templates/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/jcsoriano/laravel-crud-templates.svg?style=flat-square)](https://packagist.org/packages/jcsoriano/laravel-crud-templates)

Laravel CRUD Templates is a powerful package that allows you to quickly generate complete CRUD (Create, Read, Update, Delete) operations for your Laravel applications. With a single command, you can generate controllers, models, policies, requests, resources, migrations, factories, and tests.

## Documentation

📚 **[View Full Documentation](https://laravelcrudtemplates.com)**

### Getting Started
- [Installation](https://laravelcrudtemplates.com/guide/installation) - Requirements and installation guide
- [Quick Start](https://laravelcrudtemplates.com/guide/quick-start) - Generate your first CRUD in minutes

### Core Concepts
- [Field Types](https://laravelcrudtemplates.com/guide/field-types) - Complete list of supported field types
- [Relationships](https://laravelcrudtemplates.com/guide/relationships) - Working with model relationships
- [Generate from table](https://laravelcrudtemplates.com/guide/table-introspection) - Generate from existing database tables

### Templates
- [API Template](https://laravelcrudtemplates.com/templates/api) - RESTful API CRUD generation
- [Custom Templates](https://laravelcrudtemplates.com/templates/custom) - Create your own templates

### Advanced
- [Customizing Field Types](https://laravelcrudtemplates.com/templates/customizing-field-types) - Extend with custom field types
- [Customizing Generators](https://laravelcrudtemplates.com/templates/customizing-generators) - Override file generators
- [Customizing Printers](https://laravelcrudtemplates.com/templates/customizing-printers) - Customize code output
- [Customizing Stubs](https://laravelcrudtemplates.com/templates/customizing-stubs) - Modify stub templates

### Help
- [Troubleshooting](https://laravelcrudtemplates.com/troubleshooting) - Common issues and solutions

## Features

- **Complete CRUD Generation**: Generate all necessary files for CRUD operations in one command
- **Flexible Field Types**: Support for various field types including relationships
- **Customizable**: Extend with custom field types, generators, and templates
- **Multiple Templates**: Support for different CRUD patterns (API, Web, etc.)
- **Laravel Standards**: Generated code follows Laravel conventions and best practices
- **Generate from table**: Can generate fields from existing database tables

## Requirements

- PHP 8.4 or higher
- Laravel 11.0 or 12.0

## Installation

You can install the package via Composer:

```bash
composer require jcsoriano/laravel-crud-templates
```

The package will automatically register itself via Laravel's package discovery.

### Verify Installation

To verify the installation was successful, you can run:

```bash
php artisan crud:generate --help
```

You should see the help output for the `crud:generate` command.

### Publishing Stubs (Optional)

You can publish the stubs to customize them:

```bash
php artisan vendor:publish --tag="laravel-crud-templates-stubs"
```

## Usage

### Command Signature

```bash
php artisan crud:generate {model} [options]
```

**Arguments:**
- `model` - The name of the model (e.g., `Post`, `Content/Post` for namespaced models)

**Options:**
- `--fields=` - The fields to generate (format: `field1:type1,field2?:type2`)
- `--table=` - The database table to generate fields from
- `--template=` - The CRUD template to use (default: `api`)
- `--options=` - Additional options (format: `key1:value1,key2:value2`)
- `--force` - Overwrite existing files (default: false, will skip existing files)

### Quick Start Example

Generate a complete blog post CRUD with relationships:

```bash
php artisan crud:generate Content/Post --template=api --fields="title:string,content:text,published_at:datetime,category:belongsTo,comments:hasMany,status:enum:PublishStatus"
```

This will generate:
- `app/Http/Controllers/Api/Content/PostController.php` - RESTful API controller
- `app/Models/Content/Post.php` - Eloquent model with fillable fields and casts
- `app/Policies/Content/PostPolicy.php` - Authorization policy
- `app/Http/Requests/Content/StorePostRequest.php` - Validation for create operations
- `app/Http/Requests/Content/UpdatePostRequest.php` - Validation for update operations
- `app/Http/Resources/Content/PostResource.php` - API resource for transforming responses
- `database/migrations/{timestamp}_create_posts_table.php` - Database migration
- `database/factories/Content/PostFactory.php` - Model factory for testing
- `tests/Feature/Api/Content/PostControllerTest.php` - Feature tests
- API routes automatically added
- All generated files formatted using Pint

### Generated Routes

The generated controller provides the following RESTful API endpoints:

| HTTP Method | URI | Action | Description |
|-------------|-----|--------|-------------|
| GET | `/api/posts` | `index` | List all posts (paginated) |
| POST | `/api/posts` | `store` | Create a new post |
| GET | `/api/posts/{id}` | `show` | Show a specific post |
| PUT/PATCH | `/api/posts/{id}` | `update` | Update a post |
| DELETE | `/api/posts/{id}` | `destroy` | Delete a post |

### Response Format

All API responses follow a consistent JSON format:

**Single Resource:**
```json
{
  "data": {
    "id": 1,
    "title": "My Post",
    "content": "...",
    "published_at": "2024-01-01T00:00:00.000000Z",
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

**Collection (with pagination):**
```json
{
  "data": [
    { "id": 1, "title": "Post 1" },
    { "id": 2, "title": "Post 2" }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "per_page": 15, "total": 50 }
}
```

### Error Handling

The generated controllers handle errors appropriately:

- **404 Not Found**: When a resource doesn't exist
- **403 Forbidden**: When authorization fails
- **422 Unprocessable Entity**: When validation fails
- **500 Internal Server Error**: For unexpected errors

## What's Next?

For more advanced usage including how to create your own template, visit the [full documentation](https://laravelcrudtemplates.com).

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