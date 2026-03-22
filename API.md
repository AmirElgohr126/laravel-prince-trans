# API Documentation

Complete API reference for the Translatable Migration Builder package.

## Table of Contents

1. [Column Class](#column-class)
2. [Table Class](#table-class)
3. [MigrationGenerator](#migrationgenerator)
4. [ModelGenerator](#modelgenerator)
5. [BuilderComponent (Livewire)](#buildercomponent-livewire)

---

## Column Class

Represents a single database column with full configuration options.

**Namespace:** `Elgohr\Trans\Builders\Column`

### Constructor

```php
public function __construct(string $name = '', string $type = 'string')
```

### Methods

#### Name Management

```php
public function getName(): string
public function setName(string $name): self
```

**Example:**
```php
$column = new Column('title');
$name = $column->getName(); // 'title'
```

#### Type Management

```php
public function getType(): string
public function setType(string $type): self
```

**Example:**
```php
$column->setType('text');
$type = $column->getType(); // 'text'
```

#### Length (for string types)

```php
public function getLength(): ?int
public function setLength(?int $length): self
```

**Example:**
```php
$column->setLength(255);
```

#### Precision & Scale (for decimal types)

```php
public function getPrecision(): ?int
public function setPrecision(?int $precision): self
public function getScale(): ?int
public function setScale(?int $scale): self
```

**Example:**
```php
$column->setPrecision(10)->setScale(2);
```

#### Nullable

```php
public function isNullable(): bool
public function setNullable(bool $nullable): self
```

**Example:**
```php
$column->setNullable(true);
if ($column->isNullable()) {
    // Column can be null
}
```

#### Default Values

```php
public function getDefault(): mixed
public function setDefault(mixed $default): self
public function hasDefault(): bool
```

**Example:**
```php
$column->setDefault('active');
$column->setDefault(true);
$column->setDefault(0);

if ($column->hasDefault()) {
    $default = $column->getDefault(); // 'active'
}
```

#### Index Types

```php
public function getIndexType(): ?string
public function setIndexType(?string $indexType): self
```

**Available Types:** `'index'`, `'unique'`, `'primary'`

**Example:**
```php
$column->setIndexType('unique');
```

#### Foreign Keys

```php
public function isForeignKey(): bool
public function setForeignKey(bool $isForeignKey): self
public function getForeignTable(): ?string
public function setForeignTable(string $table): self
public function getForeignColumn(): string
public function setForeignColumn(string $column): self
public function getOnDelete(): string
public function setOnDelete(string $onDelete): self
public function getOnUpdate(): string
public function setOnUpdate(string $onUpdate): self
```

**Example:**
```php
$column->setForeignTable('users')
       ->setForeignColumn('id')
       ->setOnDelete('cascade')
       ->setOnUpdate('cascade');

if ($column->isForeignKey()) {
    $table = $column->getForeignTable(); // 'users'
}
```

#### Translatable

```php
public function isTranslatable(): bool
public function setTranslatable(bool $translatable): self
```

**Example:**
```php
$column->setTranslatable(true);
```

#### Modifiers

```php
public function getModifiers(): array
public function addModifier(string $modifier): self
```

**Example:**
```php
$column->addModifier('first');
$column->addModifier('after:other_column');
```

#### Serialization

```php
public function toArray(): array
public static function fromArray(array $data): self
```

**Example:**
```php
$data = [
    'name' => 'title',
    'type' => 'string',
    'length' => 200,
    'nullable' => false,
    'translatable' => true,
];

$column = Column::fromArray($data);
$array = $column->toArray();
```

---

## Table Class

Represents a complete table structure with multiple columns.

**Namespace:** `Elgohr\Trans\Builders\Table`

### Constructor

```php
public function __construct(string $name = '')
```

### Methods

#### Basic Properties

```php
public function getName(): string
public function setName(string $name): self
public function getPrimaryKey(): string
public function setPrimaryKey(string $primaryKey): self
public function getPrimaryKeyType(): string
public function setPrimaryKeyType(string $type): self
```

**Available Primary Key Types:** `'id'`, `'bigId'`, `'uuid'`

**Example:**
```php
$table = new Table('products');
$table->setPrimaryKey('id')
      ->setPrimaryKeyType('bigId');
```

#### Table Features

```php
public function hasTimestamps(): bool
public function setTimestamps(bool $timestamps): self
public function hasSoftDeletes(): bool
public function setSoftDeletes(bool $softDeletes): self
```

**Example:**
```php
$table->setTimestamps(true)
      ->setSoftDeletes(true);
```

#### Database Configuration

```php
public function getEngine(): ?string
public function setEngine(?string $engine): self
public function getCharset(): ?string
public function setCharset(?string $charset): self
public function getCollation(): ?string
public function setCollation(?string $collation): self
```

**Example:**
```php
$table->setEngine('InnoDB')
      ->setCharset('utf8mb4')
      ->setCollation('utf8mb4_unicode_ci');
```

#### Translation Table Configuration

```php
public function getTranslationTableName(): string
public function setTranslationTableName(?string $name): self
public function getTranslationForeignKeyName(): string
public function setTranslationForeignKeyName(?string $name): self
public function getDefaultTranslationForeignKeyName(): string
```

**Example:**
```php
$table->setTranslationTableName('product_translations')
      ->setTranslationForeignKeyName('product_id');

// Default: products_translations, products_id
```

#### Column Management

```php
public function addColumn(Column $column): self
public function addColumns(array $columns): self
public function getColumns(): Collection
public function getColumn(string $name): ?Column
public function updateColumn(string $name, Column $column): self
public function removeColumn(string $name): self
public function reorderColumns(array $names): self
```

**Example:**
```php
$title = new Column('title', 'string');
$title->setTranslatable(true);

$table->addColumn($title);

// Get all columns
$columns = $table->getColumns();

// Get specific column
$titleColumn = $table->getColumn('title');

// Reorder
$table->reorderColumns(['id', 'title', 'content', 'created_at']);
```

#### Filtering Columns

```php
public function getTranslatableColumns(): Collection
public function getNonTranslatableColumns(): Collection
```

**Example:**
```php
$translatableColumns = $table->getTranslatableColumns();
$staticColumns = $table->getNonTranslatableColumns();
```

#### Serialization

```php
public function toArray(): array
public static function fromArray(array $data): self
```

**Example:**
```php
$tableData = $table->toArray();
$table = Table::fromArray($tableData);
```

---

## MigrationGenerator

Generates Laravel migration PHP code from a Table object.

**Namespace:** `Elgohr\Trans\Generators\MigrationGenerator`

### Methods

#### Generate Migration

```php
public function generate(Table $table): string
```

Returns the complete migration file content as a string.

**Example:**
```php
$generator = new MigrationGenerator();
$code = $generator->generate($table);

// Returns:
// <?php
// use Illuminate\Database\Migrations\Migration;
// ...
```

#### Get Filename

```php
public function getFilename(Table $table): string
```

Returns the recommended filename for the migration.

**Example:**
```php
$filename = $generator->getFilename($table);
// Returns: 2024_03_22_120000_create_products_table.php
```

#### Generate Migration Code

```php
public function generateMigrationCode(Table $table, string $className): string
```

Protected method used internally.

### Complete Example

```php
use Elgohr\Trans\Builders\{Table, Column};
use Elgohr\Trans\Generators\MigrationGenerator;

// Build table
$table = new Table('products');
$table->setTimestamps(true);

// Add columns
$nameColumn = new Column('name', 'string');
$nameColumn->setLength(200)->setTranslatable(true);
$table->addColumn($nameColumn);

$descriptionColumn = new Column('description', 'text');
$descriptionColumn->setTranslatable(true);
$table->addColumn($descriptionColumn);

$priceColumn = new Column('price', 'decimal');
$priceColumn->setPrecision(10)->setScale(2)->setDefault(0);
$table->addColumn($priceColumn);

// Generate
$generator = new MigrationGenerator();
$code = $generator->generate($table);
$filename = $generator->getFilename($table);

// Save to file
file_put_contents("database/migrations/{$filename}", $code);
```

---

## ModelGenerator

Generates Laravel model PHP code with Translatable integration.

**Namespace:** `Elgohr\Trans\Generators\ModelGenerator`

### Methods

#### Generate Model

```php
public function generate(Table $table): string
```

Returns the complete model file content as a string.

**Example:**
```php
$generator = new ModelGenerator();
$code = $generator->generate($table);

// Returns:
// <?php
// namespace App\Models;
// ...
// class Product extends Model
// {
//     use Translatable;
//     ...
// }
```

### Complete Example

```php
use Elgohr\Trans\Builders\{Table, Column};
use Elgohr\Trans\Generators\ModelGenerator;

// Build table (as above)
$table = new Table('products');
// ... add columns ...

// Generate model
$generator = new ModelGenerator();
$code = $generator->generate($table);

// Save to file
file_put_contents('app/Models/Product.php', $code);
```

---

## BuilderComponent (Livewire)

Livewire component managing the entire builder UI and interactions.

**Namespace:** `Elgohr\Trans\Livewire\BuilderComponent`

### Public Properties

```php
public $tableName = '';
public $primaryKey = 'id';
public $primaryKeyType = 'id';
public $timestamps = false;
public $softDeletes = false;
public $engine = 'InnoDB';
public $charset = 'utf8mb4';
public $collation = 'utf8mb4_unicode_ci';
public $transactionTableName = '';
public $columns = [];
public $currentStep = 'basic';
public $previewMigrationCode = '';
public $previewModelCode = '';
```

### Methods

#### Column Management

```php
public function addColumn()
public function removeColumn($id)
public function updateColumn($id, $property, $value)
public function reorderColumns($newOrder)
```

**Example (in Blade):**
```blade
<button wire:click="addColumn">Add Column</button>
<input wire:model="columns.{{ $id }}.name" />
<button wire:click="removeColumn({{ $id }})">Remove</button>
```

#### Navigation

```php
public function goToStep($step)
public function resetBuilder()
```

**Available Steps:** `'basic'`, `'columns'`, `'preview'`, `'export'`

#### Export

```php
public function downloadMigration()
public function downloadModel()
public function copyToClipboard($type)
public function generateInProject()
```

`generateInProject()` writes generated artifacts directly into Laravel paths:

- `database/migrations/*_create_{table}_table.php`
- `app/Models/{Model}/{Model}.php`
- `app/Models/{Model}/{Model}Translation.php`
- `app/Models/{Model}/{Model}Repository.php`
- `app/Models/{Model}/{Model}EloquentRepository/Eloquent{Model}Repository.php`
- `database/seeders/{Model}Seeder.php`

### Events Dispatched

- `notify` - Display notification messages
- `copy-migration` - Copy migration code to clipboard
- `copy-model` - Copy model code to clipboard
- `column-added` - Column added event
- `column-removed` - Column removed event
- `column-updated` - Column updated event

### Validation Rules

```php
protected $rules = [
    'tableName' => 'required|string|regex:/^[a-z_][a-z0-9_]*$/',
    'primaryKey' => 'required|string',
];
```

---

## Configuration

Access configuration via `config()` helper or inject `config('translatable-builder')`:

```php
// Available configuration key
$enabled = config('translatable-builder.enabled');
$routePrefix = config('translatable-builder.route_prefix');
$columnTypes = config('translatable-builder.column_types');
$indexTypes = config('translatable-builder.index_types');
$seederPath = config('translatable-builder.seeder_path');
```

---

## Helper Functions (Optional)

### Get Generator Instances

```php
$migrationGenerator = app('translatable-builder.migration-generator');
$modelGenerator = app('translatable-builder.model-generator');
```

---

## Complete Workflow Example

```php
<?php

use Elgohr\Trans\Builders\{Table, Column};
use Elgohr\Trans\Generators\{
    MigrationGenerator,
    ModelGenerator
};

// 1. Create table
$table = new Table('blog_posts');
$table->setPrimaryKey('id')
      ->setPrimaryKeyType('id')
      ->setTimestamps(true)
      ->setSoftDeletes(true)
      ->setCharset('utf8mb4')
      ->setCollation('utf8mb4_unicode_ci');

// 2. Add non-translatable columns
$userId = new Column('user_id', 'foreignId');
$userId->setForeignTable('users')
       ->setForeignColumn('id')
       ->setOnDelete('cascade');
$table->addColumn($userId);

$isPublished = new Column('is_published', 'boolean');
$isPublished->setDefault(false);
$table->addColumn($isPublished);

// 3. Add translatable columns
$title = new Column('title', 'string');
$title->setLength(200)->setTranslatable(true);
$table->addColumn($title);

$content = new Column('content', 'text');
$content->setTranslatable(true);
$table->addColumn($content);

$slug = new Column('slug', 'string');
$slug->setLength(200)
     ->setTranslatable(true)
     ->setIndexType('unique');
$table->addColumn($slug);

// 4. Generate migration
$migrationGenerator = new MigrationGenerator();
$migrationCode = $migrationGenerator->generate($table);
$migrationFilename = $migrationGenerator->getFilename($table);

// 5. Generate model
$modelGenerator = new ModelGenerator();
$modelCode = $modelGenerator->generate($table);

// 6. Save files
file_put_contents("database/migrations/{$migrationFilename}", $migrationCode);
file_put_contents('app/Models/BlogPost.php', $modelCode);

// 7. Run migration
echo PHP_EOL . "Migration and Model generated successfully!" . PHP_EOL;
```

---

## Best Practices

### 1. Always Set Translatable Columns

```php
// Good
$title->setTranslatable(true);

// Bad - forgetting to set translatable
// $table->addColumn($title);
```

### 2. Use Proper Column Types

```php
// Good
$price = new Column('price', 'decimal');
$price->setPrecision(10)->setScale(2);

// Bad - using string for price
$price = new Column('price', 'string');
```

### 3. Set Foreign Key Properly

```php
// Good
$userId = new Column('user_id', 'foreignId');
$userId->setForeignTable('users');

// Acceptable - alternative method
$userId->setForeignKey(true)
        ->setForeignTable('users')
        ->setForeignColumn('id');
```

### 4. Validate Before Generating

```php
$table = new Table('products');
// ... add columns ...

// Check columns
if ($table->getColumns()->count() === 0) {
    throw new Exception('Cannot generate migration without columns');
}
```

---

## Error Handling

```php
try {
    $table = new Table('products');
    // ... configuration ...

    $generator = new MigrationGenerator();
    $code = $generator->generate($table);

    // Validate generated code
    if (empty($code)) {
        throw new Exception('Generated code is empty');
    }

    file_put_contents("database/migrations/{$filename}", $code);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

---

For more information, see [README.md](README.md) and [INSTALLATION.md](INSTALLATION.md).
