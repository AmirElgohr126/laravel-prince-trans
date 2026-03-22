# Quick Start Guide

Get the Translatable Migration Builder up and running in 5 minutes!

## Prerequisites

- Laravel 10+ with Livewire 3+
- PHP 8.1+

Astrotomic Translatable is optional.

## Installation

### 1. Install Package

```bash
composer require elgohr/trans
```

### 2. Access the Builder

Open your browser:
```
http://localhost:8000/translatable-builder
```

## First Migration in 2 Minutes

### Step 1: Basic Configuration
- **Table Name:** `products`
- ✓ **Timestamps**
- Keep other defaults

Click: **Next: Add Columns →**

### Step 2: Add Columns

Click **+ Add Column** and add:

1. **Column 1:**
   - Name: `name`
   - Type: `string`
   - Length: `200`
   - ✓ **Translatable**

2. **Column 2:**
   - Name: `description`
   - Type: `text`
   - ✓ **Translatable**

3. **Column 3:**
   - Name: `price`
   - Type: `decimal`
   - Precision: `10`, Scale: `2`
   - Default: `0`

4. **Column 4:**
   - Name: `is_active`
   - Type: `boolean`
   - Default: `true`

Click: **Next: Preview →**

### Step 3: Review

- 👀 Review the generated migration code
- 👀 Review the generated model code
- Click: **⚙️ Generate In Project** (recommended)
- Or click: **⬇️ Download Migration** and **⬇️ Download Model**

### Step 4: Use the Files

1. Place migration in `database/migrations/`
2. Place model in `app/Models/`
3. Run migration:

```bash
php artisan migrate
```

Done! 🎉

### Generated In Project Output

For table `products`, clicking `Generate In Project` creates:

- `database/migrations/*_create_products_table.php`
- `app/Models/Product/Product.php`
- `app/Models/Product/ProductTranslation.php`
- `app/Models/Product/ProductRepository.php`
- `app/Models/Product/ProductEloquentRepository/EloquentProductRepository.php`
- `database/seeders/ProductSeeder.php`

## Using Your First Model

```php
<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        // Create product
        $product = Product::create([
            'price' => $request->price,
            'is_active' => $request->is_active,
        ]);

        // Save translations (auto-handled by Translatable)
        $product->setTranslations('name', [
            'en' => 'English Name',
            'es' => 'Nombre en Español',
            'fr' => 'Nom en Français',
        ]);

        $product->setTranslations('description', [
            'en' => 'English Description',
            'es' => 'Descripción en Español',
            'fr' => 'Description en Français',
        ]);

        return response()->json($product);
    }

    public function show($id)
    {
        $product = Product::find($id);

        // Access translations
        return [
            'name' => $product->name, // Uses current locale
            'description' => $product->description,
            'name_en' => $product->translate('en')->name,
            'price' => $product->price,
        ];
    }
}
```

## Key Features You Just Used

| Feature | What It Does |
|---------|-------------|
| **Translatable Toggle** | Automatically creates translation table |
| **Type Selection** | Ensures proper column types |
| **Decimal/Precision** | Handles complex types easily |
| **Default Values** | Pre-populate fields |
| **Migration Generation** | Ready-to-use Laravel migration |
| **Model Generation** | Pre-configured with Translatable trait |

## Translatable Magic ✨

When you mark a column as "Translatable":

**Main Table (`products`):**
```sql
id | price | is_active | created_at | updated_at
```

**Translation Table (`products_translations`):**
```sql
id | products_id | locale | name | description
```

Your Laravel model handles everything transparently!

## Common Tasks

### Add More Columns

1. Go back to **Columns** step
2. Click **+ Add Column**
3. Configure and download again

### Create Another Table

1. Click **+ Add Column** until you see the preview
2. Click **Back** as needed
3. Scroll to the top and click **Reset**
4. Start fresh with new table

### Change Column Type

1. In columns step
2. Select the column
3. Change the **Type** dropdown
4. Parameters will update automatically

## Next Steps

- 📖 Read [Full Documentation](README.md)
- ⚙️ Check [Configuration](INSTALLATION.md#configuration)
- 🔌 Explore [API Documentation](API.md)
- 🎨 See [Advanced Customization](README.md#full-customization-important)

## Troubleshooting

### Can't see the builder?
```bash
# Verify installation
php artisan route:list | grep translatable

# If missing, check cache
php artisan cache:clear
php artisan route:clear
```

### Migration won't run?
```bash
# Check syntax
php artisan migrate --dry-run

# Common issue: wrong primary key
# Ensure primary key column matches config
```

### Model not translatable?
```php
// Verify in your model:
// 1. Uses Translatable trait
use Astrotomic\Translatable\Translatable;

// 2. Has $translatedAttributes set
public $translatedAttributes = ['name', 'description'];

// 3. All translatable columns are here
```

## Tips & Tricks

### 💡 Naming Convention

```
Tables: plural, snake_case
  ✓ products, user_roles, blog_posts
  ✗ Product, user_role, blogpost

Foreign Keys: singular_id
  ✓ user_id, category_id
  ✗ userId, category_fk

Translatable Fields: textual content
  ✓ title, description, content, slug
  ✗ status, count, is_active
```

### 💡 Unique Slugs per Language

```php
// In migration builder:
Column "slug":
- Type: string
- ✓ Translatable
- ✓ Unique Index

// In model:
$product->translate('en')->slug = 'english-slug';
$product->translate('es')->slug = 'spanish-slug';
$product->save();

// Query by slug in specific language
$product = Product::whereTranslation('slug', 'english-slug', 'en')->first();
```

### 💡 Default Locale Fallback

```php
// In model, add fallback
public $translationForeignKey = 'product_id';

// Use in queries
$product = Product::with('translations')->find($id);

// Access falls back to default locale
echo $product->name; // Falls back if not in current locale
```

## Common Patterns

### E-Commerce Product

```
Table: products
Columns:
- sku (string, unique)
- price (decimal)
- stock (integer)
- is_active (boolean) ✓
- name (string) ✓ translatable
- description (text) ✓ translatable
- meta_title (string) ✓ translatable
- meta_description (text) ✓ translatable
```

### Blog System

```
Table: posts
Columns:
- user_id (foreignId)
- is_published (boolean)
- published_at (dateTime)
- title (string) ✓ translatable
- content (text) ✓ translatable
- slug (string) ✓ translatable
- excerpt (text) ✓ translatable
```

### SaaS Settings

```
Table: settings
Columns:
- key (string, unique)
- value (text)
- description (text) ✓ translatable
- help_text (text) ✓ translatable
- is_public (boolean)
```

## Need Help?

1. **Quick Questions:** Check this file first
2. **Installation Issues:** See [INSTALLATION.md](INSTALLATION.md)
3. **API Help:** Check [API.md](API.md)
4. **Full Docs:** Read [README.md](README.md)
5. **Architecture:** See [ARCHITECTURE.md](ARCHITECTURE.md)

## Performance Tips

### Index Your Foreign Keys

Always add indexes to foreign keys:
- In builder: Mark `category_id` as **Index**
- Improves join performance

### Limit Translations

Don't mark every column as translatable:
- ✓ User-facing content (title, description)
- ✗ System fields (status, count, timestamps)

### Eager Load Translations

```php
// Bad - N+1 queries
Product::all()->each(fn($p) => $p->name);

// Good - single query
Product::with('translations')->get();
```

---

**Ready to build? Access the builder now:** 🚀
```
http://localhost:8000/translatable-builder
```

Or via Artisan:
```bash
php artisan translatable:builder
```

Happy building! 🎉
