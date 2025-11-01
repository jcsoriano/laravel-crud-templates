# Customizing Stubs

Stubs are template files that define the structure of generated code. By customizing stubs, you can modify the output of all generated files to match your project's conventions and requirements.

## Publishing Stubs

To customize stubs, first publish them to your project:

```bash
php artisan vendor:publish --tag="crud-templates-stubs"
```

This will copy all stub files to `stubs/` in your project root. Currently, stubs for the `api` template are available:

```
stubs/api/
├── api.controller.stub
├── api.factory.stub
├── api.migration.stub
├── api.model.stub
├── api.policy.stub
├── api.request.store.stub
├── api.request.update.stub
├── api.resource.stub
├── api.route.stub
└── api.test.stub
```

::: tip Priority
When stubs exist in your `stubs/` directory, they take priority over the package's default stubs.
:::

## Available Stubs

### Controller Stub
**File:** `api.controller.stub`

Generates the API controller with RESTful methods.

**Available Placeholders:**

::: v-pre
- `{{ RELATIONS_LIST }}` - Comma-separated list of relationship names for eager loading
:::

### Model Stub
**File:** `api.model.stub`

Generates the Eloquent model with fillable fields, casts, and relationships.

**Available Placeholders:**

::: v-pre
- `{{ FILLABLE }}` - Comma-separated fillable fields
- `{{ CASTS }}` - Model casts array
- `{{ RELATIONS }}` - Relationship methods
:::

### Policy Stub
**File:** `api.policy.stub`

Generates the authorization policy with CRUD permissions.

### Request Stubs
**Files:** `api.request.store.stub`, `api.request.update.stub`

Generate validation request classes for store and update operations.

**Available Placeholders:**

::: v-pre
- `{{ RULES }}` - Validation rules array
:::

### Resource Stub
**File:** `api.resource.stub`

Generates the API resource for transforming model data.

**Available Placeholders:**

::: v-pre
- `{{ RESOURCE_ONLY }}` - Fields to include in the only() array
- `{{ RESOURCE_RELATIONS }}` - Relationship data to include
:::

### Migration Stub
**File:** `api.migration.stub`

Generates the database migration.

**Available Placeholders:**

::: v-pre
- `{{ MIGRATION_FIELDS }}` - Migration column definitions
:::

### Factory Stub
**File:** `api.factory.stub`

Generates the model factory for testing and seeding.

**Available Placeholders:**

::: v-pre
- `{{ FACTORY_FIELDS }}` - Factory field definitions
:::

### Route Stub
**File:** `api.route.stub`

Generates the API route registration line in `routes/api.php`.

**Available Placeholders:**

::: v-pre
- `{{ MODEL }}` - Model name in StudlyCase (e.g., `Post`)
- `{{ MODEL_KEBAB_PLURAL }}` - Model name in kebab-case plural (e.g., `posts`)
:::

### Test Stub
**File:** `api.test.stub`

Generates feature tests for the controller.

**Available Placeholders:**

::: v-pre
- `{{ TEST_STRUCTURE }}` - JSON structure assertions for API responses
- `{{ DB_ASSERTION_COLUMNS }}` - Database assertion columns for tests
:::

## Common Placeholders

These placeholders are available in all stub files:

::: v-pre
| Placeholder | Description | Example |
|------------|-------------|---------|
| `{{ MODEL }}` | Model name in StudlyCase | `Post` |
| `{{ MODEL_CAMEL }}` | Model name in camelCase | `post` |
| `{{ MODEL_SNAKE }}` | Model name in snake_case | `post` |
| `{{ MODEL_KEBAB }}` | Model name in kebab-case | `post` |
| `{{ MODEL_CAMEL_PLURAL }}` | Model name in camelCase plural | `posts` |
| `{{ MODEL_SNAKE_PLURAL }}` | Model name in snake_case plural | `posts` |
| `{{ MODEL_KEBAB_PLURAL }}` | Model name in kebab-case plural | `posts` |
| `{{ NAMESPACE }}` | Namespace without leading backslash | `Content` |
| `{{ NAMESPACE_PATH }}` | Namespace path with leading backslash | `\Content` |
| `{{ NAMESPACES }}` | Auto-generated use statements | `use App\Models\Post;` |
:::

## Conditional Blocks

Stubs support conditional logic:

::: v-pre
```php
{{ if condition }}
    // Code when condition is true
{{ endif }}
```
:::

**Available Conditions:**

::: v-pre
- `{{ if scopeUser }}` - When scope option is 'user'
- `{{ if scopeTeam }}` - When scope option is 'team'
- `{{ if scopeNone }}` - When no scope is specified
- `{{ if hasRelations }}` - When model has relationships
- `{{ if hasCasts }}` - When model has type casts
:::

## Reverting to Default Stubs

To revert to the package's default stubs:

1. Delete the stub files from your `stubs/` directory
2. Or rename them (e.g., `api.controller.stub.backup`)

The package will automatically fall back to its default stubs.

## Stub Variables Reference

Each stub file has access to:
- **Common placeholders** (listed above) - Available in all stubs
- **Stub-specific placeholders** - Listed under each stub file in the [Available Stubs](#available-stubs) section
- **Conditional blocks** - For conditional logic based on your configuration

For implementation details on how variables are populated, see:
- `src/Printers/` - Classes that generate field-specific content
- `src/Generators/` - Classes that process stubs

## Next Steps

- Learn about [Customizing Field Types](/templates/customizing-field-types) to add custom field logic
- Explore [Customizing Printers](/templates/customizing-printers) to modify code snippets
