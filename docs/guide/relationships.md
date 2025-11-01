# Relationships

In addition to fields, CRUD Templates for Laravel also supports relationships. When you define a relationship field, the package automatically creates the necessary migration columns, relationship methods, and validation rules.

Relationships are declared along with fields, like below:

```bash
php artisan crud:generate Post \
  --fields="title:string,content:text,published:boolean,category:belongsTo,author:belongsTo,tags:belongsToMany,comments:morphMany"
```

::: warning Related Models Must Exist
When generating relationships, the related models must already exist in the `app/Models` directory. For example, if you're creating a `Post` model with a `category:belongsTo` relationship, the `Category` model must be created first.
:::

## Relationship Types

- [belongsTo](#belongsto)
- [hasMany](#hasmany)
- [belongsToMany](#belongstomany)
- [morphTo](#morphto)
- [morphMany](#morphmany)
- [morphToMany](#morphtomany)

### belongsTo

Creates a many-to-one relationship. The current model belongs to another model.

**Syntax:**
```bash
--fields="category:belongsTo"
```

**Migration:**
```php
$table->foreignId('category_id')->constrained();
```

This creates:
- A `category_id` column
- A foreign key constraint to the `categories` table (note: the categories table must already exist)

**Model Relationship:**
```php
public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}
```

**Validation:**
```php
'category_id' => ['required', 'exists:categories,id']
```

**Example:**
```bash
php artisan crud:generate Post --fields="title:string,category:belongsTo"
```

::: tip Naming Convention
The field name determines the related model. A field named `category` will relate to the `Category` model and create a `category_id` column.
:::

---

### hasMany

Creates a one-to-many relationship. The current model has many of another model.

**Syntax:**
```bash
--fields="posts:hasMany"
```

**Migration:**
No migration column is created (the foreign key is on the related model).

**Model Relationship:**
```php
public function posts(): HasMany
{
    return $this->hasMany(Post::class);
}
```

**Example:**
```bash
php artisan crud:generate Category --fields="name:string,posts:hasMany"
```

---

### belongsToMany

Creates a many-to-many relationship with a pivot table.

**Syntax:**
```bash
--fields="tags:belongsToMany"
```

**Migration:**
No migration column is created (requires a separate pivot table).

**Model Relationship:**
```php
public function tags(): BelongsToMany
{
    return $this->belongsToMany(Tag::class);
}
```

**Example:**
```bash
php artisan crud:generate Post --fields="title:string,content:text,tags:belongsToMany"
```

::: warning Pivot Table
You'll need to manually create the pivot table migration (e.g., `post_tag`). Follow Laravel's naming conventions: alphabetically ordered, singular model names.
:::

---

### morphTo

Creates a polymorphic relationship where the current model can belong to multiple other models.

**Syntax:**
```bash
--fields="commentable:morphTo"
```

**Migration:**
```php
$table->morphs('commentable');
```

This creates:
- `commentable_type` column (string)
- `commentable_id` column (unsignedBigInteger)

**Model Relationship:**
```php
public function commentable(): MorphTo
{
    return $this->morphTo();
}
```

**Example:**
```bash
php artisan crud:generate Comment --fields="body:text,commentable:morphTo"
```

---

### morphMany

Creates a one-to-many polymorphic relationship where the current model has many of a polymorphic model.

**Syntax:**
```bash
--fields="comments:morphMany"
```

**Migration:**
No migration column is created.

**Model Relationship:**
```php
public function comments(): MorphMany
{
    return $this->morphMany(Comment::class, 'commentable');
}
```

**Example:**
```bash
php artisan crud:generate Post --fields="title:string,content:text,comments:morphMany"
```

---

### morphToMany

Creates a many-to-many polymorphic relationship.

**Syntax:**
```bash
--fields="tags:morphToMany"
```

**Migration:**
No migration column is created.

**Model Relationship:**
```php
public function tags(): MorphToMany
{
    return $this->morphToMany(Tag::class, 'taggable');
}
```

**Example:**
```bash
php artisan crud:generate Post --fields="title:string,tags:morphToMany"
```

---

## Custom Model Paths

By default, relationship field names determine the related model (e.g., `category` relates to `Category` model). You can specify a custom model with namespace depth using a third parameter:

**Syntax:**
```bash
fieldName:relationshipType:Namespace/ModelName
```

The forward slash (`/`) separates namespace segments and maps to `App\Models\Namespace\ModelName`.

**Supported relationship types:**
- `belongsTo`
- `hasMany`
- `belongsToMany`
- `morphMany`
- `morphToMany`

**Examples:**

```bash
# Using default naming (field name determines model)
--fields="category:belongsTo"  # → App\Models\Category

# Using custom model path
--fields="category:belongsTo:Content/PostCategory"  # → App\Models\Content\PostCategory

# Multiple relationships with custom paths
php artisan crud:generate Post \
  --fields="category:belongsTo:Content/PostCategory,author:belongsTo:Users/Author,tags:belongsToMany:Taxonomy/Tag"
```

**Real-world example:**

```bash
php artisan crud:generate Article \
  --fields="title:string,content:text,blog:belongsTo:Content/Blog,author:belongsTo:Users/Author,comments:morphMany:Social/Comment"
```

This creates relationships to:
- `App\Models\Content\Blog`
- `App\Models\Users\Author`
- `App\Models\Social\Comment`

---

## Nullable Relationships

Make a relationship optional by adding `?`:

```bash
php artisan crud:generate Post --fields="title:string,category?:belongsTo"
```

This makes the foreign key nullable:

```php
$table->foreignId('category_id')->nullable()->constrained();
```

And updates validation:

```php
'category_id' => ['nullable', 'exists:categories,id']
```

## API Resources with Relationships

When relationships are defined, they're automatically included in the API resource:

```php
public function toArray($request)
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'content' => $this->content,
        'category' => new CategoryResource($this->whenLoaded('category')),
        'tags' => TagResource::collection($this->whenLoaded('tags')),
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
    ];
}
```

## Next Steps

- Learn about [Generate from Schema](/guide/generate-from-schema) to generate from existing database schemas
- Understand the [API Template](/templates/api) structure
- Explore [Customizing Field Types](/templates/customizing-field-types) to create custom relationship types

