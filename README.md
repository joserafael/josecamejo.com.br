# Jose Camejo - Personal Website & Blog

A modern, multilingual personal landing page and blog platform built with Laravel 12, featuring a comprehensive content management system and an intuitive administrative interface.

## About This Project

This is a full-featured personal website with a landing page and blog that showcases articles and professional content across three languages. Built with Laravel 12 and modern frontend technologies, it provides a robust platform for content creation and audience engagement.

## Key Features

### Multilingual Support
- Full internationalization in English, Spanish, and Portuguese
- Intelligent language detection from browser preferences, session, or cookies
- Localized URLs and content for each language
- Easy language switching for visitors

### Blog System
- Complete article management with rich content support
- Hierarchical categorization with categories and subcategories
- Flexible tagging system for content discovery
- Media gallery with image and video management
- Video uploads up to 500MB with custom thumbnails
- Nested comment system with moderation workflow
- SEO-friendly slugs and URLs

### Administrative Panel
- Secure admin authentication and authorization
- CRUD operations for all content types
- Comment moderation with approval/rejection workflows
- Bulk actions for efficient content management
- Contact form message management
- User management with role-based permissions

### Modern Frontend
- Responsive design with Tailwind CSS 4
- Interactive components with Vue 3 and Alpine.js
- Fast build process with Vite
- Optimized asset loading and caching

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3, Alpine.js, Tailwind CSS 4
- **Database**: SQLite (development), MySQL (production)
- **Build Tools**: Vite, NPM
- **Testing**: PHPUnit with comprehensive test coverage
- **Code Quality**: Laravel Pint for consistent formatting

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and NPM
- MySQL (for production) or SQLite (for development)

### Installation

1. Clone the repository
```bash
git clone <repository-url>
cd josecamejo.com.br
```

2. Install PHP dependencies
```bash
composer install
```

3. Install JavaScript dependencies
```bash
npm install
```

4. Set up environment
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your database in `.env` (SQLite is configured by default)

6. Run migrations
```bash
php artisan migrate
```

7. Create storage symbolic link
```bash
php artisan storage:link
```

8. Start development servers
```bash
composer dev
```

This will start the Laravel server, queue worker, log monitor, and Vite dev server concurrently.

## Development

### Running Tests

```bash
# Run all tests
composer test

# Run specific test suite
php artisan test --filter=BlogVideo

# Run with coverage
php artisan test --coverage
```

### Code Formatting

```bash
# Format code with Laravel Pint
./vendor/bin/pint
```

### Working with the Queue

```bash
# Start queue worker
php artisan queue:listen --tries=1
```

### Monitoring Logs

```bash
# Real-time log monitoring
php artisan pail --timeout=0
```

## Project Structure

- **`app/Models`**: Eloquent models for blog posts, comments, categories, tags, images, videos
- **`app/Http/Controllers/Admin`**: Administrative controllers for content management
- **`app/Services`**: Business logic services (e.g., CaptchaService)
- **`app/Http/Middleware`**: Custom middleware for locale detection and admin authentication
- **`routes/web.php`**: Public-facing routes
- **`routes/admin.php`**: Administrative routes
- **`resources/views`**: Blade templates
- **`resources/lang`**: Translation files for all supported languages

## Additional Documentation

- **[WARP.md](WARP.md)**: Development environment guidance and architecture overview
- **[BLOG_VIDEOS_SYSTEM.md](BLOG_VIDEOS_SYSTEM.md)**: Detailed documentation of the video management system
- **[DEPLOY_CPANEL.md](DEPLOY_CPANEL.md)**: Deployment instructions for cPanel hosting

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
