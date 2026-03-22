# Installation & Setup Guide

## Prerequisites

Before installing the Translatable Migration Builder, ensure you have:

- **PHP 8.1 or higher**
- **Laravel 10.0 or higher**
- **Composer** installed
- **Livewire 3.0 or higher**
- **Tailwind CSS** (for styling - optional but recommended)

Astrotomic Laravel Translatable is optional. If installed, generated models include trait usage automatically.

## Step-by-Step Installation

### 1. Install via Composer

```bash
composer require prince/trans
```

### 2. Install Required Dependencies (if not already installed)

#### Livewire
```bash
composer require livewire/livewire
```

#### Astrotomic Laravel Translatable (Optional)
```bash
composer require astrotomic/laravel-translatable
```

### 3. Publish Configuration (Optional)

If you want to customize the builder configuration:

```bash
php artisan vendor:publish --tag=translatable-builder-config
```

This creates `config/translatable-builder.php` in your project.

### 4. Verify Installation

Check that the package is properly registered:

```bash
php artisan list | grep translatable
```

You should see the `translatable:builder` command.

## Configuration

### Basic Configuration

The package works out-of-the-box with sensible defaults. Default config:

```php
'enabled' => true,                              // Enable/disable builder
'route_prefix' => 'translatable-builder',       // URL: /translatable-builder
'middleware' => ['web'],                        // Middleware stack
'migration_path' => 'database/migrations',      // Where to save migrations
'model_path' => 'app/Models',                   // Where to save models
'seeder_path' => 'database/seeders',            // Where to save generated seeders
'model_namespace' => 'App\\Models',             // Model namespace
```

### Custom Configuration

Edit `config/translatable-builder.php`:

```php
return [
    // Restrict to authenticated users only
    'middleware' => ['web', 'auth'],

    // Restrict to admins only (requires custom middleware)
    'middleware' => ['web', 'auth', 'admin'],

    // Disable in production
    'enabled' => env('TRANSLATABLE_BUILDER_ENABLED', true),

    // Custom paths
    'migration_path' => 'database/migrations_custom',
    'model_path' => 'app/Custom/Models',
    'model_namespace' => 'App\\Custom\\Models',

    // Add custom column types
    'column_types' => [
        // ... existing types
        'custom_type', // Your custom type
    ],
];
```

### Environment Variables (Optional)

Add to your `.env`:

```env
# Enable/disable builder (useful for production)
TRANSLATABLE_BUILDER_ENABLED=true
```

## Access the Builder

### Option 1: Web Route

Open your browser and navigate to:

```
http://localhost:8000/translatable-builder
```

### Option 2: Artisan Command

```bash
php artisan translatable:builder
```

With auto-serve option:

```bash
php artisan translatable:builder --serve
```

This will start a development server if not already running.

## Restricting Access

### Authentication Required

Modify middleware to require authentication:

```php
// config/translatable-builder.php
'middleware' => ['web', 'auth'],
```

### Role-Based Access

Create a custom middleware:

```bash
php artisan make:middleware IsAdminMiddleware
```

```php
// app/Http/Middleware/IsAdminMiddleware.php
public function handle(Request $request, Closure $next)
{
    if (! auth()->check() || ! auth()->user()->isAdmin()) {
        abort(403, 'Unauthorized access');
    }

    return $next($request);
}
```

Register and use it:

```php
// config/translatable-builder.php
'middleware' => ['web', 'auth', 'admin'],

// app/Http/Kernel.php
protected $routeMiddleware = [
    'admin' => \App\Http\Middleware\IsAdminMiddleware::class,
];
```

### Disable in Production

```env
# .env.production
TRANSLATABLE_BUILDER_ENABLED=false
```

## Styling

### Tailwind CSS

The builder uses **Tailwind CSS** for styling. Ensure Tailwind is properly configured:

#### If you don't have Tailwind yet:

```bash
npm install -D tailwindcss postcss autoprefixer
npx tailwindcss init -p
```

Configure `tailwind.config.js`:

```js
export default {
  content: [
    "./resources/views/**/*.blade.php",
    "./vendor/astrotomic/translatable-migration-builder/resources/views/**/*.blade.php",
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
```

Compile CSS:

```bash
npm run dev    # Development
npm run build  # Production
```

### Custom Styling

If you want to customize the builder's appearance, publish the views:

```bash
php artisan vendor:publish --tag=translatable-builder-views
```

This copies views to `resources/views/vendor/translatable-builder/` where you can edit them.

## Livewire Configuration

Ensure Livewire is properly configured in your project.

### Basic Setup (if needed)

```bash
composer require livewire/livewire

# Publish assets
php artisan livewire:publish
```

Include Livewire scripts in your layout:

```blade
<!-- In your main layout -->
@livereactiveScripts
```

## Verification

### Test the Installation

1. Navigate to `/translatable-builder` (or run `php artisan translatable:builder`)
2. You should see the builder UI
3. Try creating a simple table:
   - Table name: `test_products`
   - Add a column: `name` (string, translatable)
   - View preview
   - Download or copy the migration

### Check Generated Files

Generated files appear in the following paths:

- Migrations: `database/migrations/`
- Models and repositories: `app/Models/{Model}/`
- Seeder: `database/seeders/{Model}Seeder.php`

Repository pattern generated by default:

- `{Model}Repository.php` (admin methods)
- `PostEloquentRepository/Eloquent{Model}Repository.php` (extends `App\\Repositories\\EloquentBaseRepository`)

Example generated migration:

```php
// database/migrations/2024_03_22_120000_create_test_products_table.php

Schema::create('test_products', function (Blueprint $table) {
    $table->id();
    // ... columns
    $table->timestamps();
});

Schema::create('test_products_translations', function (Blueprint $table) {
    // ... translatable columns
});
```

## Troubleshooting

### Issue: Builder not loading

**Solution:**
```bash
# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Regenerate optimized files
php artisan optimize
```

### Issue: Livewire component not found

**Solution:**
```bash
# Ensure autoloading is set up
composer dump-autoload

# Clear Livewire cache
php artisan livewire:cache-manifest --anonymous
```

### Issue: 404 on `/translatable-builder`

**Solution:**
```bash
# Check routes are registered
php artisan route:list | grep translatable

# Verify package is installed
composer show astrotomic/translatable-migration-builder
```

### Issue: Styling not applied (Tailwind)

**Solution:**
1. Ensure Tailwind is configured to watch builder views:
   ```js
   // tailwind.config.js
   content: [
     "./vendor/astrotomic/translatable-migration-builder/resources/views/**/*.blade.php",
   ]
   ```

2. Rebuild CSS:
   ```bash
   npm run dev
   ```

3. Clear browser cache (Ctrl+Shift+Delete)

### Issue: Permissions denied when saving migrations

**Solution:**
```bash
# Ensure proper permissions
chmod -R 755 database/migrations
chown -R www-data:www-data database/  # Adjust ownership as needed
```

## Updating to New Versions

```bash
# Check current version
composer show astrotomic/translatable-migration-builder

# Update to latest
composer update astrotomic/translatable-migration-builder

# Republish config if needed
php artisan vendor:publish --tag=translatable-builder-config --force
```

## Next Steps

1. **Create Your First Table** - Use the builder to create your first migration
2. **Run Migrations** - Execute `php artisan migrate`
3. **Use Generated Models** - Start using your models with Translatable
4. **Read Documentation** - Check README.md for detailed usage examples

## Support

If you encounter any issues during installation:

1. Check the [troubleshooting section](#troubleshooting)
2. Review [GitHub Issues](https://github.com/Astrotomic/translatable-migration-builder/issues)
3. Check [package documentation](README.md)
4. Review [Astrotomic Translatable docs](https://github.com/Astrotomic/laravel-translatable)

---

**Happy building!** 🚀
