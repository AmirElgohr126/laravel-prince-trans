<?php

namespace Astrotomic\TranslatableMigrationBuilder\Builders;

use Illuminate\Support\Collection;

class Table
{
    /**
     * Table name
     */
    protected string $name;

    /**
     * Columns collection
     */
    protected Collection $columns;

    /**
     * Primary key name
     */
    protected string $primaryKey = 'id';

    /**
     * Primary key type
     */
    protected string $primaryKeyType = 'id';

    /**
     * Whether to add timestamps
     */
    protected bool $timestamps = false;

    /**
     * Whether to add soft deletes
     */
    protected bool $softDeletes = false;

    /**
     * Table engine (mysql)
     */
    protected ?string $engine = null;

    /**
     * Table charset
     */
    protected ?string $charset = null;

    /**
     * Table collation
     */
    protected ?string $collation = null;

    /**
     * Translation table name override
     */
    protected ?string $translationTableName = null;

    /**
     * Foreign key name in translation table
     */
    protected ?string $translationForeignKeyName = null;

    /**
     * Constructor
     */
    public function __construct(string $name = '')
    {
        $this->name = $name;
        $this->columns = collect();
    }

    // Getters and Setters
    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function setPrimaryKey(string $primaryKey): self
    {
        $this->primaryKey = $primaryKey;
        return $this;
    }

    public function getPrimaryKeyType(): string
    {
        return $this->primaryKeyType;
    }

    public function setPrimaryKeyType(string $type): self
    {
        $this->primaryKeyType = $type;
        return $this;
    }

    public function hasTimestamps(): bool
    {
        return $this->timestamps;
    }

    public function setTimestamps(bool $timestamps): self
    {
        $this->timestamps = $timestamps;
        return $this;
    }

    public function hasSoftDeletes(): bool
    {
        return $this->softDeletes;
    }

    public function setSoftDeletes(bool $softDeletes): self
    {
        $this->softDeletes = $softDeletes;
        return $this;
    }

    public function getEngine(): ?string
    {
        return $this->engine;
    }

    public function setEngine(?string $engine): self
    {
        $this->engine = $engine;
        return $this;
    }

    public function getCharset(): ?string
    {
        return $this->charset;
    }

    public function setCharset(?string $charset): self
    {
        $this->charset = $charset;
        return $this;
    }

    public function getCollation(): ?string
    {
        return $this->collation;
    }

    public function setCollation(?string $collation): self
    {
        $this->collation = $collation;
        return $this;
    }

    public function getTranslationTableName(): string
    {
        return $this->translationTableName ?? $this->name . '_translations';
    }

    public function setTranslationTableName(?string $name): self
    {
        $this->translationTableName = $name;
        return $this;
    }

    public function getTranslationForeignKeyName(): string
    {
        return $this->translationForeignKeyName ?? $this->getDefaultTranslationForeignKeyName();
    }

    public function setTranslationForeignKeyName(?string $name): self
    {
        $this->translationForeignKeyName = $name;
        return $this;
    }

    /**
     * Get default translation foreign key name
     */
    public function getDefaultTranslationForeignKeyName(): string
    {
        return strtolower($this->name) . '_id';
    }

    /**
     * Add a column
     */
    public function addColumn(Column $column): self
    {
        $this->columns->push($column);
        return $this;
    }

    /**
     * Add multiple columns
     */
    public function addColumns(array $columns): self
    {
        foreach ($columns as $column) {
            if ($column instanceof Column) {
                $this->addColumn($column);
            } elseif (is_array($column)) {
                $this->addColumn(Column::fromArray($column));
            }
        }
        return $this;
    }

    /**
     * Get all columns
     */
    public function getColumns(): Collection
    {
        return $this->columns;
    }

    /**
     * Get only translatable columns
     */
    public function getTranslatableColumns(): Collection
    {
        return $this->columns->filter(fn(Column $col) => $col->isTranslatable());
    }

    /**
     * Get non-translatable columns
     */
    public function getNonTranslatableColumns(): Collection
    {
        return $this->columns->filter(fn(Column $col) => !$col->isTranslatable());
    }

    /**
     * Get a column by name
     */
    public function getColumn(string $name): ?Column
    {
        return $this->columns->first(fn(Column $col) => $col->getName() === $name);
    }

    /**
     * Update a column
     */
    public function updateColumn(string $name, Column $column): self
    {
        $index = $this->columns->search(fn(Column $col) => $col->getName() === $name);
        if ($index !== false) {
            $this->columns->put($index, $column);
        }
        return $this;
    }

    /**
     * Remove a column by name
     */
    public function removeColumn(string $name): self
    {
        $this->columns = $this->columns->reject(fn(Column $col) => $col->getName() === $name);
        return $this;
    }

    /**
     * Reorder columns by names array
     */
    public function reorderColumns(array $names): self
    {
        $ordered = collect();
        foreach ($names as $name) {
            $column = $this->getColumn($name);
            if ($column) {
                $ordered->push($column);
            }
        }
        $this->columns = $ordered;
        return $this;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'primaryKey' => $this->primaryKey,
            'primaryKeyType' => $this->primaryKeyType,
            'timestamps' => $this->timestamps,
            'softDeletes' => $this->softDeletes,
            'engine' => $this->engine,
            'charset' => $this->charset,
            'collation' => $this->collation,
            'translationTableName' => $this->translationTableName,
            'translationForeignKeyName' => $this->translationForeignKeyName,
            'columns' => $this->columns->map(fn(Column $col) => $col->toArray())->toArray(),
        ];
    }

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        $table = new self($data['name'] ?? '');

        if (isset($data['primaryKey'])) {
            $table->setPrimaryKey($data['primaryKey']);
        }
        if (isset($data['primaryKeyType'])) {
            $table->setPrimaryKeyType($data['primaryKeyType']);
        }
        if (isset($data['timestamps'])) {
            $table->setTimestamps($data['timestamps']);
        }
        if (isset($data['softDeletes'])) {
            $table->setSoftDeletes($data['softDeletes']);
        }
        if (isset($data['engine'])) {
            $table->setEngine($data['engine']);
        }
        if (isset($data['charset'])) {
            $table->setCharset($data['charset']);
        }
        if (isset($data['collation'])) {
            $table->setCollation($data['collation']);
        }
        if (isset($data['translationTableName'])) {
            $table->setTranslationTableName($data['translationTableName']);
        }
        if (isset($data['translationForeignKeyName'])) {
            $table->setTranslationForeignKeyName($data['translationForeignKeyName']);
        }
        if (isset($data['columns'])) {
            $table->addColumns($data['columns']);
        }

        return $table;
    }
}
