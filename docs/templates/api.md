# API Template

CRUD Templates for Laravel comes with the API template out of the box. It generates a complete RESTful API with all necessary components following Laravel best practices.

## Overview

The API template creates a full-stack CRUD implementation designed for building JSON APIs. It follows RESTful conventions and includes proper authorization, validation, and testing.

## Using the Template

The API template is the default, so you don't need to specify it:

```bash
php artisan crud:generate Post --fields="title:string,content:text"
```

But you can also explicitly specify it:

```bash
php artisan crud:generate Post --template=api --fields="title:string"
```

## Scopes

The API template supports scoping resources to users or teams using the `--options` flag:

### No Scope (Default)

Resources are not scoped to any user or team:

```bash
php artisan crud:generate Post --template=api --fields="title:string"
```

### User Scope

Resources are automatically scoped to the authenticated user:

```bash
php artisan crud:generate Post --template=api --fields="title:string" --options="scope:user"
```

This adds `user_id` filtering in the controller's `index()` and `store()` methods:

```php
// Index: Only show posts belonging to the authenticated user
Post::where('user_id', Auth::id())->paginate($request->integer('per_page'))

// Store: Automatically set user_id
Post::create([
    ...$request->validated(),
    'user_id' => Auth::id(),
])
```

### Team Scope

Resources are automatically scoped to the user's current team:

```bash
php artisan crud:generate Post --template=api --fields="title:string" --options="scope:team"
```

This adds `team_id` filtering:

```php
// Index: Only show posts belonging to the user's team
Post::where('team_id', Auth::user()->current_team_id)->paginate($request->integer('per_page'))

// Store: Automatically set team_id
Post::create([
    ...$request->validated(),
    'team_id' => Auth::user()->current_team_id,
])
```

## Generated Files

When you generate a CRUD using the API template, the following files are created:

### 1. Controller (`app/Http/Controllers/Api/{Model}Controller.php`)

A RESTful controller with five methods:

```php
class PostController extends Controller
{
    public function index()      // GET /api/posts
    public function store()      // POST /api/posts
    public function show()       // GET /api/posts/{id}
    public function update()     // PUT/PATCH /api/posts/{id}
    public function destroy()    // DELETE /api/posts/{id}
}
```

**Features:**
- Paginated index listings
- Authorization via policies
- Request validation
- API resource responses
- Proper HTTP status codes

### 2. Model (`app/Models/{Model}.php`)

An Eloquent model with:

```php
class Post extends Model
{
    protected $fillable = [...];  // Based on your fields
    protected $casts = [...];     // Auto-detected from field types
    
    // Relationship methods for belongsTo, hasMany, etc.
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
```

### 3. Policy (`app/Policies/{Model}Policy.php`)

Authorization logic for all CRUD operations:

```php
class PostPolicy
{
    public function viewAny(User $user): bool
    public function view(User $user, Post $post): bool
    public function create(User $user): bool
    public function update(User $user, Post $post): bool
    public function delete(User $user, Post $post): bool
}
```

**Default Behavior:**
- All methods return `true` (you should customize this)
- Ready for your authorization logic

### 4. Request Classes

Two separate validation classes:

**StoreRequest** (`app/Http/Requests/Store{Model}Request.php`):
```php
class StorePostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'published' => 'required|boolean',
            'category_id' => 'required|exists:categories,id',
        ];
    }
}
```

**UpdateRequest** (`app/Http/Requests/Update{Model}Request.php`):
```php
class UpdatePostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'published' => 'sometimes|boolean',
            'category_id' => 'sometimes|exists:categories,id',
        ];
    }
}
```

::: tip Validation Differences
Store requests use `required` while update requests use `sometimes` to allow partial updates.
:::

### 5. API Resource (`app/Http/Resources/{Model}Resource.php`)

Transforms your model for API responses:

```php
class PostResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            ...$this->only([
                'id',
                'title',
                'content',
                'published',
                'created_at',
                'updated_at',
            ]),
            'category' => new CategoryResource($this->whenLoaded('category')),
        ];
    }
}
```

### 6. Migration (`database/migrations/{timestamp}_create_{table}_table.php`)

Complete database schema:

```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('content');
    $table->boolean('published');
    $table->foreignId('category_id')->constrained();
    $table->timestamps();
});
```

### 7. Factory (`database/factories/{Model}Factory.php`)

Model factory for testing and seeding:

```php
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'content' => fake()->paragraphs(3, true),
            'published' => fake()->boolean(),
            'category_id' => Category::factory(),
        ];
    }
}
```

### 8. Feature Test (`tests/Feature/Api/{Model}ControllerTest.php`)

Adds happy path tests for all of the endpoints generated. You're then free to add other test cases to the generated file.

```php
class PostControllerTest extends TestCase
{
    public function test_can_list_posts(): void;
    
    public function test_can_create_post(): void;

    public function test_can_show_post(): void;

    public function test_can_update_post(): void;

    public function test_can_delete_post(): void;
}
```

### 9. API Routes

Routes are automatically added to your API routes file:

```php
Route::apiResource('posts', PostController::class);
```

This creates all five RESTful endpoints:

| HTTP Method | URI | Action | Description |
|-------------|-----|--------|-------------|
| GET | `/api/posts` | `index` | List all posts (paginated) |
| POST | `/api/posts` | `store` | Create a new post |
| GET | `/api/posts/{id}` | `show` | Show a specific post |
| PUT/PATCH | `/api/posts/{id}` | `update` | Update a post |
| DELETE | `/api/posts/{id}` | `destroy` | Delete a post |

### 10. Code Formatting

Laravel Pint is automatically run to format all generated files according to Laravel's coding standards.

## Customization

You can customize the API template in several ways:

1. **[Customize Stubs](/templates/customizing-stubs)** - Modify the generated code structure
2. **[Customize Generators](/templates/customizing-generators)** - Override individual generators
3. **[Create Your Own Template](/templates/custom)** - Build a completely custom template

## Next Steps

- Learn about [Customizing Stubs](/templates/customizing-stubs) to modify generated code
- Explore [Customizing Field Types](/templates/customizing-field-types) for custom fields
- Understand [Customizing Generators](/templates/customizing-generators) to change generation logic
- Create [Your Own Template](/templates/custom) for different patterns

