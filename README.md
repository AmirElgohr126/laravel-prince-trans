# Translatable Migration Builder

A professional UI-based migration builder for Laravel that simplifies creating database tables with full translatable field support using the Astrotomic Laravel Translatable package.

## 🎯 Features

### Core Features

- **Visual UI Builder** - Intuitive interface to design database tables using a phpMyAdmin-like builder
- **Translatable Fields** - Seamlessly separate translatable and non-translatable columns
- **Automatic Translation Tables** - Auto-generates translation tables with proper constraints
- **Full Customization** - Configure every aspect of your table structure
- **Live Preview** - Preview generated migration and model code before export
- **One-Click Download** - Export migration and model files directly
- **Generate In Project** - Write migration, models, repository files, and seeder directly into your Laravel project
- **Artisan Command** - Quick access via `php artisan translatable:builder`

### Advanced Features

- Multiple primary key types (Auto-increment, Big ID, UUID)
- Foreign key management with cascade actions
- Database engine and charset configuration
- Soft deletes support
- Timestamps management
- Custom naming for translation tables and foreign keys
- Column reordering
- Edit/remove columns before generation
- Model generation with Translatable integration
- Repository generation with admin CRUD pattern
- Seeder generation for main and translation tables

## 📦 Installation

### 1. Install via Composer

```bash
composer require elgohr/trans
```

### 2. Publish Configuration (Optional)

```bash
php artisan vendor:publish --tag=translatable-builder-config
```

### 3. Requirements

- PHP 8.1+
- Laravel 10.0+
- Livewire 3.0+

Astrotomic Translatable is optional for installation. If installed, generated models include the trait usage automatically.

## 🚀 Quick Start

### Access the Builder

**Option 1: Web Route**
```
http://your-app.test/translatable-builder
```

**Option 2: Artisan Command**
```bash
php artisan translatable:builder

# With auto-serve
php artisan translatable:builder --serve
```

### Basic Workflow

1. **Step 1: Basic Configuration**
   - Enter table name
   - Configure primary key, engine, charset
   - Set timestamps, soft deletes options
   - Customize translation table names (optional)

2. **Step 2: Add Columns**
   - Click "Add Column"
   - Configure each column:
     - Name, type, length/precision
     - Nullable, default values
     - Indexes (unique, index)
     - Foreign keys
     - **Mark as "Translatable"** ✨

3. **Step 3: Preview**
   - Review generated migration
   - Review generated model
    - Copy code, download files, or generate directly in project

4. **Step 4: Use Generated Files**
   - Place migration in `database/migrations/`
   - Place model in `app/Models/`
   - Run `php artisan migrate`

5. **Optional: Generate In Project (Recommended)**
    - Click `Generate In Project` in Preview
    - The package generates automatically:
      - Migration in `database/migrations`
      - Model folder in `app/Models/{Model}`
      - `{Model}.php`
      - `{Model}Translation.php`
      - `{Model}Repository.php`
      - `{Model}EloquentRepository/Eloquent{Model}Repository.php`
      - `{Model}Seeder.php` in `database/seeders`

## 🎨 Usage Examples

### Building a Blog System

**Table Configuration:**
- Table Name: `posts`
- Timestamps: ✓
- Soft Deletes: ✓

**Columns:**
1. `user_id` (foreignId) → `users.id`
2. `title` (string, 200) → **Translatable** ✓
3. `slug` (string, 200) → **Translatable** ✓
4. `content` (text) → **Translatable** ✓
5. `excerpt` (text, nullable) → **Translatable** ✓
6. `is_published` (boolean, default: false)
7. `published_at` (dateTime, nullable)
8. `view_count` (integer, default: 0)

**Generated Files:**

Migration:
```php
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->boolean('is_published')->default(false);
    $table->dateTime('published_at')->nullable();
    $table->integer('view_count')->default(0);
    $table->softDeletes();
    $table->timestamps();
});

Schema::create('posts_translations', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug');
    $table->text('content');
    $table->text('excerpt')->nullable();
    $table->string('locale')->index();
    $table->unique(['posts_id', 'locale']);
    $table->foreign('posts_id')->references('id')->on('posts')->cascadeOnDelete();
});
```

Model:
```php
class Post extends Model
{
    use Translatable;
    
    public $translatedAttributes = ['title', 'slug', 'content', 'excerpt'];
    
    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'view_count' => 'integer',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

### Building a Product Catalog

**Table Configuration:**
- Table Name: `products`
- Timestamps: ✓
- Engine: InnoDB
- Charset: utf8mb4

**Columns:**
1. `sku` (string, 100, unique)
2. `price` (decimal, 10, 2, default: 0)
3. `stock` (integer, default: 0, nullable)
4. `is_active` (boolean, default: true)
5. `category_id` (foreignId) → `categories.id`
6. `name` (string, 200) → **Translatable** ✓
7. `description` (text) → **Translatable** ✓
8. `slug` (string) → **Translatable** ✓

## ⚙️ Configuration

### Config File: `config/translatable-builder.php`

```php
return [
    // Enable/disable builder
    'enabled' => env('TRANSLATABLE_BUILDER_ENABLED', true),

    // Route configuration
    'route_prefix' => 'translatable-builder',
    'middleware' => ['web'],

    // File paths
    'migration_path' => 'database/migrations',
    'model_path' => 'app/Models',
    'seeder_path' => 'database/seeders',
    'model_namespace' => 'App\\Models',

    // Available column types
    'column_types' => [
        'string', 'text', 'integer', 'bigInteger', 'decimal',
        'float', 'boolean', 'date', 'dateTime', 'time', 'json',
        'jsonb', 'uuid', 'enum', 'foreignId', 'morphs',
    ],

    // Available index types
    'index_types' => [
        'index' => 'Index',
        'unique' => 'Unique',
        'primary' => 'Primary Key',
    ],
];
```

## 🔑 Key Concepts

### Translatable Columns

When you mark a column as "Translatable":
- It's **removed** from the main table
- It's **added** to the translations table
- The builder automatically handles:
  - Translation table creation
  - Locale column (indexed)
  - Foreign key to main table
  - Unique constraint on `[foreign_key, locale]`

### Example Structure

**Before (without Translations):**
```php
$table->string('name');
$table->text('description');
```

**After (with Translations):**
```
Main Table (products):
- id
- sku
- price
- stock
- created_at
- updated_at

Translations Table (products_translations):
- id
- products_id (foreign key)
- locale (indexed)
- name
- description
- unique(products_id, locale)
```

## 📁 Package Structure

```
translatable-migration-builder/
├── config/
│   └── translatable-builder.php          # Configuration
├── resources/
│   ├── views/
│   │   ├── index.blade.php               # Main view
│   │   └── livewire/
│   │       └── builder.blade.php         # Livewire component
│   ├── css/
│   │   └── builder.css                   # Styling (optional)
│   ├── js/
│   │   └── builder.js                    # JavaScript (optional)
│   └── examples/
│       ├── 2024_03_22_120000_create_products_table.php
│       └── Product.php
├── routes/
│   └── web.php                           # Routes
├── src/
│   ├── Builders/
│   │   ├── Column.php                    # Column model
│   │   └── Table.php                     # Table model
│   ├── Commands/
│   │   └── LaunchBuilderCommand.php      # Artisan command
│   ├── Generators/
│   │   ├── MigrationGenerator.php        # Migration code generator
│   │   └── ModelGenerator.php            # Model code generator
│   ├── Http/
│   │   └── Controllers/
│   │       └── BuilderController.php     # Route controller
│   ├── Livewire/
│   │   └── BuilderComponent.php          # Livewire component
│   ├── Support/                          # Helper utilities (optional)
│   ├── Contracts/                        # Interfaces (optional)
│   └── TranslatableMigrationBuilderServiceProvider.php
├── composer.json
└── README.md
```

## 🛠️ Architecture

### Builder Classes

**Column.php** - Represents a single database column
```php
$column = new Column('title', 'string');
$column->setLength(200)
       ->setNullable(false)
       ->setTranslatable(true);
```

**Table.php** - Represents a complete table structure
```php
$table = new Table('products');
$table->setPrimaryKey('id')
      ->setTimestamps(true)
      ->addColumn($column);
```

### Generators

**MigrationGenerator.php** - Generates Laravel migration PHP code
```php
$generator = new MigrationGenerator();
$code = $generator->generate($table);
$filename = $generator->getFilename($table);
```

**ModelGenerator.php** - Generates Laravel model with Translatable trait
```php
$generator = new ModelGenerator();
$code = $generator->generate($table);
```

### Livewire Component

**BuilderComponent.php** - Manages UI state and interactions
- `addColumn()` - Add new column
- `removeColumn()` - Remove column
- `updateColumn()` - Update column properties
- `goToStep()` - Navigate between steps
- `generatePreview()` - Generate preview code
- `downloadMigration()` - Export migration file
- `downloadModel()` - Export model file
- `generateInProject()` - Generate migration, models, repository files, and seeder directly in Laravel project

## 🎯 Best Practices

### Table Naming

```php
// Use plural form for tables
products, users, posts, categories

// Translation tables auto-generated as
products_translations, users_translations, etc.
```

### Column Naming

```php
// Use snake_case
user_id, category_id, product_name

// Foreign keys follow convention
{singular_table}_id (user_id, category_id)
```

### Translatable Fields

```php
// Good - Textual content
title, description, content, slug, meta_title, meta_description

// Bad - Config/status fields (should NOT be translatable)
is_active, status, price, stock
```

## 🔒 Security

### Access Control

By default, the builder is accessible to all authenticated users. Restrict access by modifying middleware:

```php
// config/translatable-builder.php
'middleware' => ['web', 'auth', 'admin'], // Add custom middleware
```

### Environment Control

```php
// .env
TRANSLATABLE_BUILDER_ENABLED=true  # Set to false in production
```

## 🐛 Troubleshooting

### Builder not loading?
1. Ensure Livewire is properly installed
2. Check if route is accessible: `php artisan route:list | grep translatable`
3. Verify `TRANSLATABLE_BUILDER_ENABLED` is `true` in .env

### Generated migration fails?
1. Check table name doesn't conflict with existing tables
2. Verify foreign key references exist
3. Ensure column names are valid MySQL identifiers

### Model not working with Translatable?
1. Verify `$translatedAttributes` are set correctly
2. Check foreign key names match migration
3. Ensure Astrotomic package is installed

## 📚 Additional Resources

- [Astrotomic Translatable Docs](https://github.com/Astrotomic/laravel-translatable)
- [Laravel Migrations](https://laravel.com/docs/migrations)
- [Livewire Documentation](https://livewire.laravel.com/)

## 📝 License

MIT License - See LICENSE file

## 🤝 Contributing

Contributions are welcome! Please submit issues and pull requests.

## 📧 Support

For issues, questions, or feature requests, please open an issue on GitHub.

---

**Built with ❤️ for Laravel developers who value clean, translatable databases**
