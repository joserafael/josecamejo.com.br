# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

This is a personal landing page  and blog website built with Laravel 12 (PHP 8.2+), featuring multilingual support (English, Spanish, Portuguese), a full blog system with comments, and an administrative panel. The frontend uses Vue 3, Alpine.js, and Tailwind CSS 4.

## Common Commands

### Development
```bash
# Start all development services (server, queue, logs, vite)
composer dev

# Start Laravel development server only
php artisan serve

# Start Vite dev server for frontend assets
npm run dev

# Build frontend assets for production
npm run build

# Watch queue for background jobs
php artisan queue:listen --tries=1

# Monitor logs in real-time
php artisan pail --timeout=0
```

### Testing
```bash
# Run all tests
composer test
# OR
php artisan test

# Run specific test filter
php artisan test --filter=BlogVideo

# Run tests with coverage
php artisan test --coverage
```

### Database
```bash
# Run migrations
php artisan migrate

# Run migrations and seeders
php artisan migrate --seed

# Rollback last migration
php artisan migrate:rollback

# Fresh migration (drops all tables)
php artisan migrate:fresh

# Create new migration
php artisan make:migration create_table_name
```

### Code Quality
```bash
# Format code with Laravel Pint
./vendor/bin/pint

# Clear application cache
php artisan cache:clear

# Clear config cache
php artisan config:clear

# Clear all caches
php artisan optimize:clear

# Create storage symbolic link
php artisan storage:link
```

## Architecture

### Multi-Language System
- **Supported Languages**: English (en), Spanish (es), Portuguese (pt)
- **Locale Middleware**: `SetLocale` - Detects language from route parameter, session, cookie, or browser preference
- **Route Structure**: Most routes support language prefix (e.g., `/en/blog`, `/es/blog`, `/pt/blog`)
- **Fallback**: Portuguese is the default locale
- **Translation Files**: Located in `resources/lang/{locale}/`

### Authentication & Authorization
- **Admin Middleware**: `AdminMiddleware` - Checks if user is authenticated and has `is_admin` flag
- **Admin Routes**: All prefixed with `/admin` and protected by admin middleware
- **Login**: Standard Laravel authentication at `/login`

### Routing Structure
- **`routes/web.php`**: Public routes (home, blog, comments, language switcher)
- **`routes/admin.php`**: Administrative routes (included from web.php with `/admin` prefix)
- **`routes/api.php`**: API routes
- **`routes/console.php`**: Console commands

### Blog System
The blog is a core feature with these main models:
- **BlogPost**: Main blog content with multilingual support
- **BlogCategory**: Top-level blog categorization
- **BlogSubcategory**: Secondary categorization
- **BlogTag**: Tagging system
- **BlogImage**: Media gallery for blog posts
- **BlogVideo**: Video management system (see BLOG_VIDEOS_SYSTEM.md)
- **BlogComment**: Nested comment system with approval workflow

All blog models use:
- Soft deletes
- Slug generation
- Language filtering scopes
- Active/inactive status

### Blog Video System
Videos are managed through a complete CRUD system:
- **Upload**: Supports MP4, AVI, MOV, WMV (max 500MB)
- **Thumbnails**: Optional custom thumbnails (max 5MB)
- **Storage**: `storage/app/public/blog/videos/` for videos, `storage/app/public/blog/videos/thumbnails/` for thumbnails
- **Metadata**: Tracks duration, dimensions, file size, mime type
- See `BLOG_VIDEOS_SYSTEM.md` for full documentation

### Admin Panel Structure
Controllers are organized in `app/Http/Controllers/Admin/`:

- **BlogPostController**: Blog article management
- **BlogCategoryController**: Blog categories
- **BlogSubcategoryController**: Blog subcategories
- **BlogTagController**: Blog tags
- **BlogImageController**: Blog media gallery
- **BlogVideoController**: Blog video management
- **BlogCommentController**: Comment moderation (approve/reject, bulk actions)
- **MessageController**: Contact form messages
- **UserController**: User management

### Frontend Architecture
- **CSS Framework**: Tailwind CSS 4 with @tailwindcss/forms plugin
- **JavaScript Frameworks**: Vue 3 and Alpine.js
- **Build Tool**: Vite
- **Assets**: Entry points at `resources/css/app.css` and `resources/js/app.js`

### Services
- **CaptchaService**: Generates captcha for contact forms

### Testing Configuration
- **Framework**: PHPUnit
- **Test Database**: MySQL (`josecamejo_test`)
- **Coverage Reports**: Generated in `build/coverage/`
- **Strict Mode**: Enabled (fails on warnings, risky tests, empty suites)
- **Excluded from Coverage**: Kernel, Handler, Middleware, RouteServiceProvider

### Database
- **Default (Development)**: SQLite (`database/database.sqlite`)
- **Production**: MySQL (configure via .env)
- **Testing**: MySQL (`josecamejo_test` database)
- **Factories**: Available for all blog models
- **Seeders**: Located in `database/seeders/`

## Important Conventions

### Model Structure
All blog models follow these patterns:
1. Use `HasFactory` trait for testing
2. Implement `SoftDeletes` for safe deletion
3. Define `$fillable` arrays for mass assignment
4. Create scopes for common queries (e.g., `scopeActive`, `scopeByLanguage`)
5. Implement slug generation in model events

### Request Validation
Form validation is handled by dedicated Request classes in `app/Http/Requests/`. Always create a FormRequest for new CRUD operations.

### File Uploads
Video/image uploads should:
1. Validate file types and sizes
2. Generate unique filenames
3. Store original filename
4. Save to appropriate storage disk
5. Create database records with file metadata

### Admin Routes
When adding new admin functionality:
1. Create controller in `app/Http/Controllers/Admin/`
2. Add routes to `routes/admin.php`
3. Apply `admin` middleware
4. Create corresponding views in `resources/views/admin/`

### Multilingual Content
When creating content that supports multiple languages:
1. Add `language` field to migration (enum: 'en', 'es', 'pt')
2. Implement `scopeByLanguage()` in model
3. Use locale middleware on routes
4. Store translations in `resources/lang/{locale}/`

## File Storage

- **Public Files**: Use `storage/app/public/` and link with `php artisan storage:link`
- **Blog Videos**: `storage/app/public/blog/videos/`
- **Blog Images**: Configure in `config/blog.php`
- **Public Access**: Files accessible via `/storage/` URL path

## Environment Setup

1. Copy `.env.example` to `.env`
2. Generate application key: `php artisan key:generate`
3. Configure database in `.env` (SQLite by default)
4. Run migrations: `php artisan migrate`
5. Create storage link: `php artisan storage:link`
6. Install npm dependencies: `npm install`
7. Start development: `composer dev`
