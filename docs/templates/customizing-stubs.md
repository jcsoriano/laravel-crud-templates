# Customizing Stubs

Stubs are template files that define the structure of generated code. By customizing stubs, you can modify the output of all generated files to match your project's conventions and requirements.

## Publishing Stubs

To customize stubs, first publish them to your project:

```bash
php artisan vendor:publish --tag="laravel-crud-templates-stubs"
```

This will copy all stub files to `stubs/` in your project root:

```
stubs/
├── crud.controller.stub
├── crud.factory.stub
├── crud.migration.stub
├── crud.model.stub
├── crud.policy.stub
├── crud.request.store.stub
├── crud.request.update.stub
├── crud.resource.stub
└── crud.test.stub
```

::: tip Priority
When stubs exist in your `stubs/` directory, they take priority over the package's default stubs.
:::

## Available Stubs

### Controller Stub
**File:** `crud.controller.stub`

Generates the API controller with RESTful methods.

### Model Stub
**File:** `crud.model.stub`

Generates the Eloquent model with fillable fields, casts, and relationships.

### Policy Stub
**File:** `crud.policy.stub`

Generates the authorization policy with CRUD permissions.

### Request Stubs
**Files:** `crud.request.store.stub`, `crud.request.update.stub`

Generate validation request classes for store and update operations.

### Resource Stub
**File:** `crud.resource.stub`

Generates the API resource for transforming model data.

### Migration Stub
**File:** `crud.migration.stub`

Generates the database migration.

### Factory Stub
**File:** `crud.factory.stub`

Generates the model factory for testing and seeding.

### Test Stub
**File:** `crud.test.stub`

Generates feature tests for the controller.

## Stub Placeholders

Stubs use placeholders that are replaced with actual values during generation. Here are the available placeholders:

### Basic Placeholders

| Placeholder | Description | Example |
|------------|-------------|---------|
| `{{ MODEL }}` | Model name in StudlyCase | `Post` |
| `{{ MODEL_CAMEL }}` | Model name in camelCase | `post` |
| `{{ MODEL_PLURAL }}` | Model name in plural | `posts` |
| `{{ TABLE }}` | Database table name | `posts` |
| `{{ NAMESPACE_PATH }}` | Namespace path for nested models | `\Content` |
| `{{ NAMESPACES }}` | Auto-generated use statements | `use App\Models\Post;` |

### Field-Specific Placeholders

| Placeholder | Description |
|------------|-------------|
| `{{ FILLABLE }}` | Comma-separated fillable fields |
| `{{ CASTS }}` | Model casts array |
| `{{ RELATIONS }}` | Relationship methods |
| `{{ MIGRATIONS }}` | Migration column definitions |
| `{{ RULES }}` | Validation rules array |
| `{{ FACTORY }}` | Factory field definitions |
| `{{ RESOURCE }}` | Resource array fields |
| `{{ DB_ASSERTION_COLUMNS }}` | Database assertion columns for tests |

### Conditional Blocks

Stubs support conditional logic:

```php
{{ if condition }}
    // Code when condition is true
{{ endif }}
```

**Available Conditions:**

- `{{ if scopeUser }}` - When scope option is 'user'
- `{{ if scopeTeam }}` - When scope option is 'team'
- `{{ if scopeNone }}` - When no scope is specified
- `{{ if hasRelations }}` - When model has relationships
- `{{ if hasCasts }}` - When model has type casts

## Testing Custom Stubs

After customizing stubs, test them by generating a CRUD:

```bash
php artisan crud:generate TestModel --fields="name:string,active:boolean"
```

Review the generated files to ensure your customizations are applied correctly.

## Reverting to Default Stubs

To revert to the package's default stubs:

1. Delete the stub files from your `stubs/` directory
2. Or rename them (e.g., `crud.controller.stub.backup`)

The package will automatically fall back to its default stubs.

## Best Practices

1. **Version Control**: Commit your custom stubs to version control
2. **Documentation**: Document your customizations for your team
3. **Test First**: Test on a dummy model before using in production
4. **Incremental Changes**: Make small changes and test each one
5. **Backup**: Keep backups of working stub configurations

## Stub Variables Reference

For a complete list of available variables and how they're populated, see the generator classes in the package:

- `src/Printers/` - Classes that generate field-specific content
- `src/Generators/` - Classes that process stubs

## Next Steps

- Learn about [Customizing Field Types](/templates/customizing-field-types) to add custom field logic
- Explore [Customizing Generators](/templates/customizing-generators) to override generation behavior
- Create [Your Own Template](/templates/custom) for completely different patterns
