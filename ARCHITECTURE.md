# Architecture & Design

Comprehensive overview of the package architecture and design patterns.

## Table of Contents

1. [Overview](#overview)
2. [Core Architecture](#core-architecture)
3. [Design Patterns](#design-patterns)
4. [Component Interactions](#component-interactions)
5. [Data Flow](#data-flow)
6. [Extensibility](#extensibility)
7. [Best Practices Applied](#best-practices-applied)

---

## Overview

The Translatable Migration Builder is built using a layered architecture with clear separation of concerns:

```
┌─────────────────────────────────────┐
│        UI Layer (Livewire)          │
│   (BuilderComponent, Views/Blade)   │
├─────────────────────────────────────┤
│     Business Logic Layer             │
│   (Builders, Generators)            │
├─────────────────────────────────────┤
│       Data Models                    │
│   (Column, Table)                   │
├─────────────────────────────────────┤
│    Laravel Framework Core            │
│   (Routing, Middleware, Config)     │
└─────────────────────────────────────┘
```

---

## Core Architecture

### 1. Data Models (`Builders` Layer)

#### Column Model

**Responsibility:** Represent a single database column with all configuration options.

```php
class Column
{
    // Essential properties
    protected string $name;
    protected string $type;
    
    // Optional properties
    protected ?int $length;
    protected ?int $precision;
    protected ?int $scale;
    protected bool $nullable;
    protected mixed $default;
    
    // Constraints
    protected ?string $indexType;
    protected bool $isForeignKey;
    
    // Translation support
    protected bool $translatable;
}
```

**Key Features:**
- Fluent interface for building
- Array serialization (JSON-safe)
- Type validation and conversion
- State persistence

#### Table Model

**Responsibility:** Aggregate multiple columns into a table definition.

```php
class Table
{
    protected string $name;
    protected Collection $columns;
    protected string $primaryKey;
    protected bool $timestamps;
    protected bool $softDeletes;
    // ... more properties
}
```

**Key Features:**
- Column collection management
- Filtering (translatable/non-translatable)
- Reordering support
- Translation table customization
- Array serialization

### 2. Generator Layer

#### MigrationGenerator

**Responsibility:** Convert Table objects into Laravel migration PHP code.

```php
class MigrationGenerator
{
    public function generate(Table $table): string
    public function generateMigrationCode(...): string
    public function getFilename(Table $table): string
    
    // Protected helpers
    protected function generateMainTableSchema(...): string
    protected function generateTranslationTableSchema(...): string
    protected function generateColumnsCode(...): string
    protected function generateColumnCode(...): string
}
```

**Process:**
1. Separate translatable/non-translatable columns
2. Generate main table schema
3. Generate translation table schema (if needed)
4. Create migration class wrapper
5. Return complete PHP code

**Translation Table Logic:**
- Auto-creates translation table name
- Adds `locale` column (indexed)
- Creates unique constraint: `[foreign_key, locale]`
- Sets up proper foreign key relationships

#### ModelGenerator

**Responsibility:** Generate Laravel models with Translatable trait integration.

```php
class ModelGenerator
{
    public function generate(Table $table): string
    
    // Helpers
    protected function generateTranslatedAttributes(...): string
    protected function generateCasts(...): string
    protected function generateRelationships(...): string
}
```

**Generates:**
- Model class with namespace
- Translatable trait integration
- `$translatedAttributes` property
- Type casts based on column types
- BelongsTo relationships for foreign keys

### 3. UI Layer (Livewire Component)

#### BuilderComponent

**Responsibility:** Manage the interactive UI and state.

```php
class BuilderComponent extends Component
{
    // Configuration state
    public $tableName;
    public $primaryKey;
    public $timestamps;
    // ... more properties
    
    // Workflow state
    public $columns = [];
    public $currentStep;
    
    // Output
    public $previewMigrationCode;
    public $previewModelCode;
}
```

**Key Methods:**
- `addColumn()` - Add new column to collection
- `removeColumn($id)` - Remove column
- `updateColumn($id, $property, $value)` - Update column property
- `reorderColumns($newOrder)` - Reorder columns
- `goToStep($step)` - Navigate workflow
- `generatePreview()` - Generate output code
- `downloadMigration()` - Return migration file
- `downloadModel()` - Return model file

**Validation:**
- Table name format (snake_case)
- At least one column required
- No duplicate column names

---

## Design Patterns

### 1. Builder Pattern

Used for constructing Column and Table objects:

```php
// Fluent interface (Builder pattern)
$column = new Column('title', 'string');
$column->setLength(200)
       ->setNullable(false)
       ->setTranslatable(true);

// Or traditional
$column = new Column('title');
$column->setType('string');
$column->setLength(200);
```

**Benefits:**
- Readable, intuitive API
- Method chaining
- Easy to understand
- Type-safe through setter returns

### 2. Repository Pattern

Models act as data repositories:

```php
// Column repository
$column = new Column('title', 'string');
$columnData = $column->toArray();           // Hydrate
$column = Column::fromArray($columnData);   // Rehydrate

// Table repository
$tableData = $table->toArray();
$table = Table::fromArray($tableData);
```

**Benefits:**
- Persistence abstraction
- State serialization
- Easy data migration
- Session-friendly

### 3. Strategy Pattern

Generators implement different strategies:

```php
interface Generator {
    public function generate(Table $table): string;
}

// Two strategies
$migrationGenerator = new MigrationGenerator();
$modelGenerator = new ModelGenerator();

// Same interface, different implementations
$migrationCode = $migrationGenerator->generate($table);
$modelCode = $modelGenerator->generate($table);
```

### 4. Separation of Concerns

Clear responsibility boundaries:

```
Column/Table (Data)
    ↓
Generators (Transformation)
    ↓
Livewire Component (Presentation)
    ↓
Blade Views (Rendering)
```

Each layer focuses on one concern and doesn't know about others.

### 5. Collection Pattern

Laravel Collections for flexible column management:

```php
$table = new Table('products');

// Add columns
$table->addColumn($nameColumn);
$table->addColumn($descriptionColumn);

// Filter
$translatable = $table->getTranslatableColumns();  // Returns Collection
$static = $table->getNonTranslatableColumns();     // Returns Collection

// Chain operations
$specificColumns = $table->getColumns()
    ->filter(fn($col) => $col->getType() === 'string')
    ->map(fn($col) => $col->getName());
```

---

## Component Interactions

### Request Flow Diagram

```
┌─────────────────┐
│   User Action   │ (Click "Add Column")
└────────┬────────┘
         │
         ↓
┌──────────────────────────┐
│  Livewire Component      │ (BuilderComponent)
│  addColumn() method      │
└────────┬─────────────────┘
         │
         ↓
┌──────────────────────────┐
│  Update @livewire        │ (Send new $columns array)
│  property: columns       │
└────────┬─────────────────┘
         │
         ↓
┌──────────────────────────┐
│  Re-render Blade view    │ (Display updated form)
│  with new columns        │
└────────┬─────────────────┘
         │
         ↓
┌──────────────────────────┐
│  Browser Updates         │ (Show new column input)
│  (Livewire magic!)       │
└──────────────────────────┘
```

### Preview Generation Flow

```
┌──────────────────────┐
│ User clicks Preview  │
└──────────┬───────────┘
           │
           ↓
┌──────────────────────────────────┐
│ BuilderComponent::goToStep('p... │
│ validateBasicInfo()              │
└──────────┬───────────────────────┘
           │
           ↓
┌──────────────────────────────────┐
│ buildTableObject()               │ (Create Table from Livewire state)
│ Build Column objects from $columns
└──────────┬───────────────────────┘
           │
           ↓
    ┌──────┴──────┐
    │             │
    ↓             ↓
┌──────────────┐  ┌────────────────┐
│ Migration    │  │ Model          │
│ Generator    │  │ Generator      │
└──────┬───────┘  └────────┬───────┘
       │                   │
       ↓                   ↓
┌──────────────────────────────────┐
│ Set @ properties:                │
│ previewMigrationCode             │
│ previewModelCode                 │
└──────────────┬───────────────────┘
               │
               ↓
         ┌───────────┐
         │ Re-render │
         │ preview   │
         └───────────┘
```

---

## Data Flow

### Building a Table

```php
// 1. User navigates Basic Info
$componentState = [
    'tableName' => 'products',
    'primaryKey' => 'id',
    'timestamps' => true,
];

// 2. User adds columns
$columns = [
    ['name' => 'name', 'type' => 'string', 'translatable' => true],
    ['name' => 'price', 'type' => 'decimal', 'precision' => 10, 'scale' => 2],
];

// 3. Livewire builds Table object
$table = Table::fromArray([
    'name' => 'products',
    'columns' => $columns,
    'timestamps' => true,
]);

// 4. Generator receives Table
$migrationCode = $migrationGenerator->generate($table);

// 5. Output sent to view
view('translatable-builder::livewire.builder', [
    'previewMigrationCode' => $migrationCode,
]);
```

### State Serialization

```
Browser (JSON) ← Livewire (PHP Array) ← Model Objects (PHP)
    ↓                  ↓                      ↓
serialized         $columns = [         Column object
Column data        'name' => ...,        Table object
                   'type' => ...,
                ]
```

---

## Extensibility

### Adding Custom Column Types

```php
// In config/translatable-builder.php
'column_types' => [
    'string', 'text', // existing types
    'custom_type',    // your custom type
],

// Or extend in service provider
public function boot()
{
    config(['translatable-builder.column_types' => [
        ...config('translatable-builder.column_types'),
        'my_custom_type',
    ]]);
}
```

### Custom Generator

```php
namespace App\Generators;

use Astrotomic\TranslatableMigrationBuilder\Builders\Table;

class CustomGenerator
{
    public function generate(Table $table): string
    {
        // Custom generation logic
    }
}

// Register in service provider
$this->app->singleton('custom-generator', CustomGenerator::class);
```

### Custom Middleware

```php
// Restrict to admin only
// config/translatable-builder.php
'middleware' => ['web', 'auth', 'admin'],
```

### Custom Views

```bash
# Publish views
php artisan vendor:publish --tag=translatable-builder-views

# Edit resources/views/vendor/translatable-builder/**
```

---

## Best Practices Applied

### 1. **Dependency Injection**

```php
// Service Provider
public function register(): void
{
    $this->app->singleton(
        'translatable-builder.migration-generator',
        fn($app) => new MigrationGenerator()
    );
}
```

### 2. **Configuration Management**

```php
// Centralized config
config('translatable-builder.enabled')
config('translatable-builder.column_types')
config('translatable-builder.middleware')

// Publishable for customization
php artisan vendor:publish --tag=translatable-builder-config
```

### 3. **Type Hinting**

```php
public function generate(Table $table): string { ... }
public function getColumns(): Collection { ... }
public function setName(string $name): self { ... }
```

### 4. **Immutability Patterns**

Fluent setters return `$this`:

```php
$table->setName('products')
      ->setTimestamps(true)
      ->setSoftDeletes(true);
```

### 5. **Error Handling**

```php
protected function validateBasicInfo(): bool
{
    if (empty($this->tableName)) {
        $this->dispatch('notify', 
            message: 'Table name required', 
            type: 'error'
        );
        return false;
    }
    return true;
}
```

### 6. **Testing Considerations**

Structure for easy testing:

```php
// Testable builders
$table = Table::fromArray($testData);
$migration = $generator->generate($table);
$this->assertStringContainsString('Schema::create', $migration);

// Testable models
$column = new Column('test', 'string');
$this->assertEquals('test', $column->getName());
```

### 7. **Documentation**

```php
/**
 * Generate migration code
 *
 * @param Table $table The table to generate migration for
 * @return string The complete migration PHP code
 * 
 * @throws Exception If table is invalid
 * 
 * @example
 * $table = new Table('products');
 * $generator = new MigrationGenerator();
 * $code = $generator->generate($table);
 */
public function generate(Table $table): string { ... }
```

### 8. **PSR Standards**

- **PSR-4:** Autoloading (composer.json setup)
- **PSR-12:** Code style (Pint formatting)
- **PSR-3:** Logging (Laravel logger)
- **PSR-7:** Messages (Laravel request/response)

---

## Performance Considerations

### 1. Collection Usage

```php
// Efficient filtering
$translatable = $table->getTranslatableColumns();  // Single pass
// vs.
$cols = $table->getColumns();
$trans = [];
foreach ($cols as $col) {
    if ($col->isTranslatable()) {
        $trans[] = $col;  // Inefficient
    }
}
```

### 2. String Building

```php
// Efficient
$code = <<<PHP
Schema::create('...')
PHP;

// Instead of
$code = 'Schema::create(';
$code .= "'...'" . ')';  // Multiple allocations
```

### 3. Livewire State Management

```php
// Only essential data in Livewire
public $columns = [];  // Lightweight array

// Avoid
public $allColumnTypes = []; // Bloated state
```

---

## Security Considerations

### 1. Input Validation

```php
protected $rules = [
    'tableName' => 'required|string|regex:/^[a-z_][a-z0-9_]*$/',
];
```

### 2. Authentication

```php
'middleware' => ['web', 'auth'],
```

### 3. SQL Injection Prevention

- Laravel Schema builder handles escaping
- No raw SQL in generators

### 4. Environment Control

```env
# Disable in production
TRANSLATABLE_BUILDER_ENABLED=false
```

---

## Future Extension Points

### Possible Enhancements

1. **Template System**
   - Save/load configurations
   - Column templates
   - Pre-built schemas

2. **Advanced Generators**
   - Factory generator
   - Seeder generator
   - Test generator
   - Request/FormRequest generator

3. **Batch Operations**
   - Bulk column import
   - Schema cloning
   - Multi-table creation

4. **Database Integration**
   - Reverse engineer from existing tables
   - Compare with existing schema
   - Migration preview execution

5. **API Layer**
   - JSON endpoints
   - Programmatic access
   - CI/CD integration

---

**The architecture prioritizes clarity, extensibility, and maintainability while following Laravel and PHP best practices.**
