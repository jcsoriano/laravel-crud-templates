# Quick Start

## Command Signature

You can generate entire CRUD features with a single command. Below is the command signature:

```
php artisan crud:generate
  {model : The name of the model}
  {--fields= : The fields to generate. Format: field1:type1,field2?:type2}
  {--table= : The database table to generate the fields from}
  {--template=api : The CRUD template to generate}
  {--skip= : List of files you want to skip}
  {--options= : Other options to pass to the generator. Format: key1:value1,key2:value2}
  {--force : Overwrite existing files}
```

## Your First CRUD Generation

Let's go straight ahead and generate a simple blog post CRUD:

```bash
php artisan crud:generate Content/Post \
--template=api \
--fields="title:string,content:text,published_at:datetime,category:belongsTo,comments:hasMany,status:enum:PublishStatus" \
--options="scope:user"
```

This example demonstrates multiple field types including relationships, datetime, and enums.

## Generated Files

The above command will generate the following files:

- `app/Http/Controllers/Api/Content/PostController.php`
- `app/Models/Content/Post.php`
- `app/Policies/Content/PostPolicy.php`
- `app/Http/Requests/Content/StorePostRequest.php`
- `app/Http/Requests/Content/UpdatePostRequest.php`
- `app/Http/Resources/Content/PostResource.php`
- `database/migrations/{timestamp}_create_posts_table.php`
- `database/factories/Content/PostFactory.php`
- `tests/Feature/Api/Content/PostControllerTest.php`
- API routes automatically added to `routes/api.php` (will run `install:api` if the file doesn't exist yet)
- Laravel Pint run on all generated files

::: tip File Protection
By default, existing files will not be overwritten. Use the `--force` flag to overwrite existing files:
```bash
php artisan crud:generate Content/Post --fields="..." --force
```
:::

::: warning Related Model or Enum Requirement
When including relations and enums in `--fields=`, the related model or enum must already exist.
:::

## Model Enhancements

The generated `Post` model will include several automatic enhancements:

#### Relationship Methods:
```php
public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}

public function comments(): HasMany
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



## Relationships

Special handling for relationships is already applied to the Resource file, the migration, and the validation rules.

### Resource File

The generated `PostResource` automatically includes relationships:

```php
public function toArray($request): array
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'content' => $this->content,
        'published_at' => $this->published_at,
        'status' => $this->status,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
        'category' => CategoryResource::make($this->whenLoaded('category')),
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
        'title' => ['required', 'string', 'max:255'],
        'content' => ['required', 'string'],
        'published_at' => ['required', 'date'],
        'category_id' => ['bail', 'required', 'exists:categories,id'],
        'status' => ['required', Rule::enum(PublishStatus::class)],
    ];
}
```

## Generated Routes

The command will also automatically register the following routes in your `routes/api.php` file:

| HTTP Method | URI | Action | Description |
|-------------|-----|--------|-------------|
| GET | `/api/posts` | `index` | List all posts (paginated) |
| POST | `/api/posts` | `store` | Create a new post |
| GET | `/api/posts/{id}` | `show` | Show a specific post |
| PUT/PATCH | `/api/posts/{id}` | `update` | Update a post |
| DELETE | `/api/posts/{id}` | `destroy` | Delete a post |

### Response Format

All responses follow a consistent JSON format:

#### Single Resource:
```json
{
  "data": {
    "id": 1,
    "title": "My Post",
    "content": "...",
    "category": { "...": "..." },
    "comments": [ { "...": "..." } ],
    "status": "published",
    "published_at": "2024-01-01T00:00:00.000000Z",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

#### Collection (with pagination):
```json
{
  "data": [
    { "The Post object as above" },
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "per_page": 15, "total": 50 }
}
```

### Error Handling

The generated routes will handle errors appropriately:

- **404 Not Found**: When a resource doesn't exist
- **403 Forbidden**: When authorization fails
- **422 Unprocessable Entity**: When validation fails
- **500 Internal Server Error**: For unexpected errors

## Next Steps

Now that you've generated your first CRUD, explore more advanced features:

- Explore the [API Template](/templates/api) which is used by the above command
- Learn about all available [Field Types](/guide/field-types) and how to use them
