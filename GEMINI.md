# Project Overview

**Jose Camejo - Personal Website & Blog**

A modern, multilingual personal landing page and blog platform featuring a comprehensive content management system and an intuitive administrative interface. The platform supports English, Spanish, and Portuguese, and includes an advanced blog system with category, tag, image, and video management functionalities.

## Tech Stack
- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3, Alpine.js, Tailwind CSS 4
- **Database**: SQLite (Development) / MySQL (Production)
- **Build Tools**: Vite, NPM
- **Testing**: PHPUnit

# Building and Running

## Initial Setup
1. Install PHP dependencies: `composer install`
2. Install Node dependencies: `npm install`
3. Set up environment variables: `cp .env.example .env`
4. Generate application key: `php artisan key:generate`
5. Run database migrations: `php artisan migrate`
6. Create storage symbolic link: `php artisan storage:link`

## Running the Application
To run the development environment, execute:
```bash
composer dev
```
*This command runs the Laravel server, queue worker, log monitor (`pail`), and Vite dev server concurrently using `npx concurrently`.*

## Testing
Run all tests using Composer:
```bash
composer test
```
Or use artisan directly:
```bash
php artisan test
```

## Code Quality
Code formatting is enforced using Laravel Pint:
```bash
./vendor/bin/pint
```

# Development Conventions
- **MVC Architecture**: Follows standard Laravel conventions. Models are located in `app/Models/`, Controllers in `app/Http/Controllers/`.
- **Admin Panel**: Administrative functionality is separated into `app/Http/Controllers/Admin/` and uses specific routes defined in `routes/admin.php`.
- **Localization**: Translation strings are stored in `resources/lang/` for multi-language support (English, Spanish, Portuguese).
- **Frontend**: The UI is built using Blade templates (`resources/views/`), enhanced with Vue 3 components and Alpine.js for interactivity, and styled with Tailwind CSS. Assets are bundled with Vite.
- **Documentation**: For more detailed insights, refer to `WARP.md` (architecture), `BLOG_VIDEOS_SYSTEM.md` (video handling), and `DEPLOY_CPANEL.md` (deployment).