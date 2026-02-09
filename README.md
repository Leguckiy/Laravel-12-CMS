# Laravel 12 CMS

A content management system built with [Laravel 12](https://laravel.com), taking inspiration from [PrestaShop](https://www.prestashop.com) and [OpenCart](https://www.opencart.com). It provides a multi-language storefront with categories, products, filters, and a separate admin panel for managing content, settings, and users.

## Requirements

- PHP 8.2+
- Composer
- MySQL / MariaDB or another database supported by Laravel

## Getting Started

### 1. Install dependencies

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configure `.env` with your database credentials and app URL.

### 2. Run migrations

Create all database tables:

```bash
php artisan migrate
```

### 3. Seed the database

Populate the database with initial data (languages, currencies, categories, products, pages, users, settings, etc.):

```bash
php artisan db:seed
```

### 4. Copy images

Images used by seeded products and categories are copied from `resources/seed-images` into `storage/app/public`. Place your image files in `resources/seed-images` (keeping the structure expected by seeders), then run:

```bash
php artisan images:add
```

Ensure the public storage link exists so images are served correctly:

```bash
php artisan storage:link
```

### 5. Run the application

```bash
php artisan serve
```

Then open the given URL in your browser (e.g. `http://localhost:8000`). Use the `{lang}` prefix for the front (e.g. `/uk` or `/en`).

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
