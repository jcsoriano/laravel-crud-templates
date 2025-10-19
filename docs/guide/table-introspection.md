# Generate from Table

CRUD Templates for Laravel allows you to generate CRUD files from existing database tables. This is especially useful when you're adding the package to an existing project or want to generate CRUDs for tables that already exist.

## Basic Usage

Use the `--table` option to specify an existing database table:

```bash
php artisan crud:generate Post --table=posts
```

This will:
1. Connect to your database
2. Read the table schema
3. Detect column types and generate appropriate field types
4. Create all CRUD files based on the existing structure

## Column Type Mapping

The package automatically maps database column types to field types:

| Database Column | Field Type | Model Cast |
|----------------|------------|------------|
| `varchar`, `char` | `string` | - |
| `text`, `longtext` | `text` | - |
| `int`, `integer` | `integer` | - |
| `decimal`, `float` | `decimal` | `decimal:2` |
| `boolean`, `tinyint(1)` | `boolean` | `boolean` |
| `date` | `date` | `immutable_date` |
| `datetime`, `timestamp` | `datetime` | `immutable_datetime` |
| `json` | `json` | `array` |

## Nullable Columns

Nullable columns in the database are automatically detected and handled:

```sql
CREATE TABLE posts (
    id BIGINT UNSIGNED PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT NULL,  -- This will be detected as nullable
    published_at DATETIME NULL  -- This will also be nullable
);
```

Running introspection:

```bash
php artisan crud:generate Post --table=posts
```

The generated model will properly handle nullable fields in validation and allow null values.

## Foreign Keys

Foreign key columns (typically ending with `_id`) are detected and converted to `belongsTo` relationships:

```sql
CREATE TABLE posts (
    id BIGINT UNSIGNED PRIMARY KEY,
    category_id BIGINT UNSIGNED,
    author_id BIGINT UNSIGNED,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (author_id) REFERENCES users(id)
);
```

The package will generate:
- `category:belongsTo` relationship
- `author:belongsTo` relationship

## Combining with Custom Fields

You can combine table introspection with additional custom fields:

```bash
php artisan crud:generate Post --table=posts --fields="featured:boolean,tags:belongsToMany"
```

This will:
1. Generate fields from the `posts` table
2. Add the `featured` boolean field
3. Add the `tags` belongsToMany relationship

### Overriding Table Fields

If you specify a field that already exists in the table, your custom definition will override the introspected field:

```bash
php artisan crud:generate Post --table=posts --fields="status:enum:PostStatus"
```

If the `posts` table has a `status` column, it will be replaced with your enum definition instead of being introspected.

## Limitations

### 1. Relationships Beyond belongsTo

Table introspection can only detect `belongsTo` relationships (foreign keys on the current table). Other relationship types must be manually specified:

```bash
php artisan crud:generate Category \
  --table=categories \
  --fields="posts:hasMany"
```

### 2. Enum Types

Database enum columns are converted to string fields. If you need enum casting, specify it manually:

```bash
php artisan crud:generate Order \
  --table=orders \
  --fields="status:enum:OrderStatus"
```

### 3. Polymorphic Relationships

Polymorphic columns (`*_type` and `*_id` pairs) are automatically detected. For example, if your table has `commentable_type` and `commentable_id` columns, a `morphTo` relation will be created with the name `commentable`.

**Nullable Polymorphic Relationships:**
If both the `*_type` and `*_id` columns are nullable, the generated migration will use `nullableMorphs()` instead of `morphs()`.

Example table:
```sql
CREATE TABLE comments (
    id BIGINT UNSIGNED PRIMARY KEY,
    content TEXT NOT NULL,
    commentable_type VARCHAR(255) NULL,
    commentable_id BIGINT UNSIGNED NULL
);
```

Running introspection will automatically generate:
```bash
php artisan crud:generate Comment --table=comments
```

The generated model will include:
```php
public function commentable(): MorphTo
{
    return $this->morphTo();
}
```

### 4. Custom Column Types

Columns with custom or unsupported types will default to `string`. You may need to manually specify the field type.

## Practical Examples

### Existing Blog

You have an existing blog database:

```sql
CREATE TABLE posts (
    id BIGINT UNSIGNED PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT NULL,
    published_at DATETIME NULL,
    category_id BIGINT UNSIGNED,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

Generate CRUD with additional relationships:

```bash
php artisan crud:generate Post \
  --table=posts \
  --fields="tags:belongsToMany,comments:morphMany"
```

### E-commerce Products

Existing products table:

```sql
CREATE TABLE products (
    id BIGINT UNSIGNED PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sku VARCHAR(100) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    is_active BOOLEAN DEFAULT 1
);
```

Generate CRUD and add relationships:

```bash
php artisan crud:generate Product \
  --table=products \
  --fields="category:belongsTo,reviews:hasMany"
```

### Migration Behavior with --table

When using the `--table` option, the migration file is automatically skipped since the table already exists:

```bash
php artisan crud:generate User --table=users
```

You'll see a warning message:
```
⚠ --table used so the table already exists. Skipping migration generation
```

This prevents creating duplicate migrations for existing tables. All other CRUD files (models, controllers, etc.) will still be generated normally.

## Database Connection

Table introspection uses your default database connection defined in `config/database.php`. Make sure:

1. Your database connection is properly configured
2. The database is accessible
3. The table exists before running the command

## Next Steps

- Explore the [API Template](/templates/api) to understand generated structure
- Learn about [Customizing Templates](/templates/customizing-stubs) to modify output
- Check [Troubleshooting](/troubleshooting) for common issues

