# Generate from Schema

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

Running generate from table:

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

## Polymorphic Relationships

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

Running generate from table will automatically generate:
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

## Other Relationships

You can combine generate from schema with `--fields` option to specify other relationship types:

```bash
php artisan crud:generate Post --table=posts --fields="featured:boolean,tags:belongsToMany,comments:morphMany"
```

## Overriding Table Fields

If you specify a field that already exists in the table, your custom definition will override the default field type:

```bash
php artisan crud:generate Post --table=posts --fields="status:enum:PostStatus"
```

If the `posts` table has a `status` column, it will be replaced with your enum definition instead of defaulting to a `string` field.

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

Generate from table uses your default database connection defined in `config/database.php`. Make sure:

1. Your database connection is properly configured
2. The database is accessible
3. The table exists before running the command

## Next Steps

- Explore the [API Template](/templates/api) to understand generated structure
- Learn about [Customizing Templates](/templates/customizing-stubs) to modify output
- Check [Troubleshooting](/troubleshooting) for common issues


