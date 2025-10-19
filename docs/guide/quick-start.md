# Quick Start

## Command Signature

The main command for generating CRUD files is:

```bash
php artisan crud:generate {model} [options]
```

### Arguments

- `model` - The name of the model (e.g., `Post`, `Content/Post` for namespaced models)

### Options

- `--fields=` - The fields to generate (format: `field1:type1,field2?:type2`)
- `--table=` - The database table to generate fields from
- `--template=` - The CRUD template to use (default: `api`)
- `--options=` - Additional options (format: `key1:value1,key2:value2`)
- `--force` - Overwrite existing files (default: false, will skip existing files)

::: tip Namespaced Models
You can organize models with namespaces by using a forward slash in the model name, e.g., `Content/Post` will create the model at `app/Models/Content/Post.php`
:::

## Your First CRUD Generation

Let's generate a simple blog post CRUD:

```bash
php artisan crud:generate Content/Post --template=api --fields="title:string,content:text,published_at:datetime,category:belongsTo,comments:hasMany,status:enum:PublishStatus"
```

This example demonstrates multiple field types including relationships, datetime, and enums.

## Generated Routes

This single command will generate the following fully functioning routes for you:

| HTTP Method | URI | Action | Description |
|-------------|-----|--------|-------------|
| GET | `/api/posts` | `index` | List all posts (paginated) |
| POST | `/api/posts` | `store` | Create a new post |
| GET | `/api/posts/{id}` | `show` | Show a specific post |
| PUT/PATCH | `/api/posts/{id}` | `update` | Update a post |
| DELETE | `/api/posts/{id}` | `destroy` | Delete a post |

## Response Format

All responses follow a consistent JSON format:

**Single Resource:**
```json
{
  "data": {
    "id": 1,
    "title": "My Post",
    "content": "...",
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

**Collection:**
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

## Error Handling

The generated controllers handle errors appropriately:

- **404 Not Found**: When a resource doesn't exist
- **403 Forbidden**: When authorization fails
- **422 Unprocessable Entity**: When validation fails
- **500 Internal Server Error**: For unexpected errors

## Generated Files

To make those routes work, all appropriate files are generated: models, controllers, policies, requests, resources, migrations, factories, and even tests!

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

::: tip File Protection
By default, existing files will not be overwritten. Use the `--force` flag to overwrite existing files:
```bash
php artisan crud:generate Content/Post --fields="..." --force
```
:::

## Understanding Generated Code

### Model Enhancements

The generated `Post` model includes several automatic enhancements:

**Relationship Methods:**
```php
public function category()
{
    return $this->belongsTo(Category::class);
}

public function comments()
{
    return $this->hasMany(Comment::class);
}
```

**Type Casting:**
```php
protected $casts = [
    'published_at' => 'immutable_datetime',  // Automatic datetime casting
    'status' => PublishStatus::class,        // Enum casting
];
```

**Fillable Fields:**
```php
protected $fillable = [
    'title',
    'content',
    'published_at',
    'category_id',
    'status',
];
```

### Resource Transformation

The generated `PostResource` automatically includes relationships:

```php
public function toArray($request): array
{
    return [
        ...$this->only([
            'id',
            'title',
            'content',
            'published_at',
            'status',
            'created_at',
            'updated_at',
        ]),
        'category' => new CategoryResource($this->whenLoaded('category')),
        'comments' => CommentResource::collection($this->whenLoaded('comments')),
    ];
}
```

### Migration with Foreign Keys

The migration includes proper foreign key constraints:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('content');
    $table->dateTime('published_at');
    $table->foreignId('category_id')->constrained();
    $table->string('status');
    $table->timestamps();
});
```

### Validation Rules

The request classes automatically include appropriate validation:

**StorePostRequest:**
```php
public function rules(): array
{
    return [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'published_at' => 'required|date',
        'category_id' => 'required|exists:categories,id',
        'status' => ['required', Rule::enum(PublishStatus::class)],
    ];
}
```

::: tip Enum Requirement
Before running the generation, make sure the `PublishStatus` enum exists at `app/Enums/PublishStatus.php`:
```php
<?php

namespace App\Enums;

enum PublishStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
}
```
:::

## Next Steps

Now that you've generated your first CRUD, explore more advanced features:

- Learn about all available [Field Types](/guide/field-types)
- Add [Relationships](/guide/relationships) between models
- Use [Table Introspection](/guide/table-introspection) to generate from existing tables
- Explore [Templates](/templates/api) to understand the generated structure

