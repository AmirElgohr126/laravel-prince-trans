<?php

namespace Elgohr\Trans\Builders;

class Column
{
    /**
     * Column name
     */
    protected string $name;

    /**
     * Column type (string, text, integer, etc.)
     */
    protected string $type = 'string';

    /**
     * Column length for string types
     */
    protected ?int $length = null;

    /**
     * Precision for decimal types
     */
    protected ?int $precision = null;

    /**
     * Scale for decimal types
     */
    protected ?int $scale = null;

    /**
     * Whether the column is nullable
     */
    protected bool $nullable = false;

    /**
     * Default value
     */
    protected mixed $default = null;

    /**
     * Whether column has a default
     */
    protected bool $hasDefault = false;

    /**
     * Index type: 'index', 'unique', 'primary'
     */
    protected ?string $indexType = null;

    /**
     * Whether this is a foreign key
     */
    protected bool $isForeignKey = false;

    /**
     * Foreign key table
     */
    protected ?string $foreignTable = null;

    /**
     * Foreign key column
     */
    protected ?string $foreignColumn = 'id';

    /**
     * Foreign key on delete action
     */
    protected string $onDelete = 'cascade';

    /**
     * Foreign key on update action
     */
    protected string $onUpdate = 'cascade';

    /**
     * Whether this column is translatable
     */
    protected bool $translatable = false;

    /**
     * Additional modifiers
     */
    protected array $modifiers = [];

    /**
     * Constructor
     */
    public function __construct(string $name = '', string $type = 'string')
    {
        $this->name = $name;
        $this->type = $type;
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(?int $length): self
    {
        $this->length = $length;
        return $this;
    }

    public function getPrecision(): ?int
    {
        return $this->precision;
    }

    public function setPrecision(?int $precision): self
    {
        $this->precision = $precision;
        return $this;
    }

    public function getScale(): ?int
    {
        return $this->scale;
    }

    public function setScale(?int $scale): self
    {
        $this->scale = $scale;
        return $this;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function setNullable(bool $nullable): self
    {
        $this->nullable = $nullable;
        return $this;
    }

    public function getDefault(): mixed
    {
        return $this->default;
    }

    public function setDefault(mixed $default): self
    {
        $this->default = $default;
        $this->hasDefault = true;
        return $this;
    }

    public function hasDefault(): bool
    {
        return $this->hasDefault;
    }

    public function getIndexType(): ?string
    {
        return $this->indexType;
    }

    public function setIndexType(?string $indexType): self
    {
        $this->indexType = $indexType;
        return $this;
    }

    public function isForeignKey(): bool
    {
        return $this->isForeignKey;
    }

    public function setForeignKey(bool $isForeignKey): self
    {
        $this->isForeignKey = $isForeignKey;
        return $this;
    }

    public function getForeignTable(): ?string
    {
        return $this->foreignTable;
    }

    public function setForeignTable(string $table): self
    {
        $this->foreignTable = $table;
        $this->isForeignKey = true;
        return $this;
    }

    public function getForeignColumn(): string
    {
        return $this->foreignColumn;
    }

    public function setForeignColumn(string $column): self
    {
        $this->foreignColumn = $column;
        return $this;
    }

    public function getOnDelete(): string
    {
        return $this->onDelete;
    }

    public function setOnDelete(string $onDelete): self
    {
        $this->onDelete = $onDelete;
        return $this;
    }

    public function getOnUpdate(): string
    {
        return $this->onUpdate;
    }

    public function setOnUpdate(string $onUpdate): self
    {
        $this->onUpdate = $onUpdate;
        return $this;
    }

    public function isTranslatable(): bool
    {
        return $this->translatable;
    }

    public function setTranslatable(bool $translatable): self
    {
        $this->translatable = $translatable;
        return $this;
    }

    public function getModifiers(): array
    {
        return $this->modifiers;
    }

    public function addModifier(string $modifier): self
    {
        $this->modifiers[] = $modifier;
        return $this;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'length' => $this->length,
            'precision' => $this->precision,
            'scale' => $this->scale,
            'nullable' => $this->nullable,
            'default' => $this->default,
            'hasDefault' => $this->hasDefault,
            'indexType' => $this->indexType,
            'isForeignKey' => $this->isForeignKey,
            'foreignTable' => $this->foreignTable,
            'foreignColumn' => $this->foreignColumn,
            'onDelete' => $this->onDelete,
            'onUpdate' => $this->onUpdate,
            'translatable' => $this->translatable,
            'modifiers' => $this->modifiers,
        ];
    }

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        $column = new self($data['name'] ?? '', $data['type'] ?? 'string');

        if (isset($data['length'])) {
            $column->setLength($data['length']);
        }
        if (isset($data['precision'])) {
            $column->setPrecision($data['precision']);
        }
        if (isset($data['scale'])) {
            $column->setScale($data['scale']);
        }
        if (isset($data['nullable'])) {
            $column->setNullable($data['nullable']);
        }
        if (($data['hasDefault'] ?? false) === true) {
            $column->setDefault($data['default']);
        }
        if (isset($data['indexType'])) {
            $column->setIndexType($data['indexType']);
        }
        if (isset($data['isForeignKey'])) {
            $column->setForeignKey($data['isForeignKey']);
        }
        if (isset($data['foreignTable'])) {
            $column->setForeignTable($data['foreignTable']);
        }
        if (isset($data['foreignColumn'])) {
            $column->setForeignColumn($data['foreignColumn']);
        }
        if (isset($data['onDelete'])) {
            $column->setOnDelete($data['onDelete']);
        }
        if (isset($data['onUpdate'])) {
            $column->setOnUpdate($data['onUpdate']);
        }
        if (isset($data['translatable'])) {
            $column->setTranslatable($data['translatable']);
        }
        if (isset($data['modifiers'])) {
            foreach ($data['modifiers'] as $modifier) {
                $column->addModifier($modifier);
            }
        }

        return $column;
    }
}
