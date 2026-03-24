# AGENTS.md - Codebase Guidelines

This is a Laravel 12 + Vue 3 + Tailwind CSS multilingual personal website and blog platform.

## Build/Lint/Test Commands

### PHP Commands (Laravel)

```bash
# Run all tests
composer test
# or: php artisan test

# Run a single test class
php artisan test tests/Feature/BlogImageControllerTest.php

# Run a single test method
php artisan test --filter=test_admin_can_store_blog_image

# Run tests matching a pattern
php artisan test --filter=BlogPost

# Run tests with coverage
php artisan test --coverage

# Run tests in parallel (faster)
php artisan test --parallel

# Clear config cache
php artisan config:clear

# Format code with Laravel Pint
./vendor/bin/pint

# Run Pint in check mode (no changes)
./vendor/bin/pint --test

# Start dev servers concurrently
composer dev

# Individual servers
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

### Frontend Commands

```bash
# Build assets for production
npm run build

# Start Vite dev server
npm run dev
```

### Database

```bash
# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Refresh and seed
php artisan migrate:fresh --seed
```

## Code Style Guidelines

### General

- Use 4 spaces for indentation (no tabs)
- Always use strict typing (`declare(strict_types=1);`)
- PSR-4 autoloading convention
- Follow Laravel best practices and conventions

### PHP Formatting

- Opening `<?php` on first line, no newline before it
- Namespace on line 1, blank line before class declaration
- Use `array` syntax instead of `[]` for array type hints
- One blank line between use statements and class definition
- Use single quotes for strings unless interpolation needed
- Align chained methods on separate lines

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $posts = BlogPost::with(['category', 'author'])
            ->published()
            ->inLanguage()
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('blog.index', compact('posts'));
    }
}
```

### Naming Conventions

| Type | Convention | Example |
|------|------------|---------|
| Models | PascalCase singular | `BlogPost`, `BlogCategory` |
| Controllers | PascalCase + Controller | `BlogPostController`, `Admin/BlogPostController` |
| Tables | snake_case plural | `blog_posts`, `blog_categories` |
| Methods | camelCase | `getSidebarData()`, `toggleFeatured()` |
| Variables | camelCase | `$blogPosts`, `$validatedData` |
| Routes | kebab-case | `blog-posts.index`, `admin.blog-images.store` |
| Views | kebab-case | `admin.blog-posts.index`, `blog.show` |
| Migrations | timestamp_description | `2025_09_26_193034_create_blog_categories_table` |
| Factories | Model + Factory | `BlogPostFactory` |
| Request classes | Purpose + Request | `BlogVideoRequest`, `StoreCommentRequest` |

### Controllers

- Use type hints for all method parameters
- Return `View` for renders, `RedirectResponse` for redirects
- Keep controllers thin; delegate business logic to models/services
- Validate requests using Form Request classes
- Use dependency injection for services

```php
public function store(BlogVideoRequest $request): RedirectResponse
{
    $validated = $request->validated();
    // Handle business logic...
    return redirect()->route('admin.blog-videos.index')
                    ->with('success', 'Video created successfully.');
}
```

### Models (Eloquent)

- Use `$fillable` for mass assignment
- Use `$casts` for attribute type conversion
- Define relationships with explicit return types
- Use scopes for common queries (prefix with `scope`)
- Use model factories for testing

```php
class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'content', 'status'];
    
    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
```

### Form Requests

- Create dedicated Request classes for validation
- Always implement `authorize()` method
- Provide custom error messages in `messages()` method
- Use `Rule::in()` for enum-like validations

```php
public function rules(): array
{
    return [
        'title' => ['required', 'string', 'max:255'],
        'language' => ['required', 'string', Rule::in(['pt', 'en', 'es'])],
        'video' => ['nullable', 'file', 'mimes:mp4,mov,webm', 'max:512000'],
    ];
}
```

### Views (Blade)

- Use kebab-case for view names
- Use component syntax for reusable UI
- Pass data via `compact()` or array syntax
- Use `@forelse` for empty-state handling

### Testing

- One assertion per test when practical
- Use descriptive test names: `test_admin_can_view_blog_images_index`
- Use factories for test data
- Use `RefreshDatabase` trait for database tests
- Use `actingAs()` for authentication in feature tests
- Test should fail on unexpected behavior (strict mode enabled)

```php
public function test_admin_can_store_blog_image(): void
{
    $this->actingAs($this->admin);
    
    $response = $this->post(route('admin.blog-images.store'), [
        'title' => 'Test Image',
        'language' => 'pt',
    ]);
    
    $response->assertRedirect(route('admin.blog-images.index'));
    $this->assertDatabaseHas('blog_images', ['title' => 'Test Image']);
}
```

### Migrations

- Use anonymous class migrations (Laravel 7+)
- Always include `up()` and `down()` methods
- Add indexes for foreign keys and frequently queried columns
- Use `$table->foreignId()` for foreign keys

```php
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_categories');
    }
};
```

### Factories

- Extend `Illuminate\Database\Eloquent\Factories\Factory`
- Use Faker for test data
- Define state methods for common variations

```php
public function definition(): array
{
    return [
        'title' => $this->faker->sentence(6, true),
        'status' => $this->faker->randomElement(['draft', 'published']),
    ];
}

public function published(): static
{
    return $this->state(fn (array $attributes) => [
        'status' => 'published',
    ]);
}
```

### Error Handling

- Use `firstOrFail()` for model lookups
- Return session flash messages for success/error feedback
- Use `withErrors()` for validation errors
- Log errors using Laravel's `Log` facade

### Routes

- Group related routes with `Route::group()`
- Use named routes (`->name('route.name')`)
- Use controller method syntax: `[Controller::class, 'method']`
- Prefix admin routes with `admin.` namespace

### Database

- Primary database: MySQL (production), SQLite (testing)
- Use migrations for all schema changes
- Foreign keys should be explicitly named
- Use enum sparingly; prefer string with validation

## Project Structure

```
app/
├── Console/Commands/      # Artisan commands
├── Helpers/               # Helper functions
├── Http/
│   ├── Controllers/       # Application controllers
│   │   ├── Admin/        # Admin controllers
│   │   └── Auth/         # Authentication controllers
│   ├── Middleware/       # Custom middleware
│   └── Requests/         # Form request classes
├── Models/               # Eloquent models
├── Providers/            # Service providers
└── Services/             # Business logic services

database/
├── factories/            # Model factories
├── migrations/          # Database migrations
└── seeders/              # Database seeders

routes/
├── web.php               # Public routes
└── admin.php             # Admin routes

tests/
├── Feature/              # Feature tests
└── Unit/                # Unit tests
```

## Environment

- PHP 8.2+
- Laravel 12
- Vue 3 + Alpine.js + Tailwind CSS 4
- Supported languages: `pt`, `en`, `es`
