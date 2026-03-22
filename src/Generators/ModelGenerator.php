<?php

namespace Astrotomic\TranslatableMigrationBuilder\Generators;

use Astrotomic\TranslatableMigrationBuilder\Builders\Table;
use Illuminate\Support\Str;

class ModelGenerator
{
    /**
     * Generate model code
     */
    public function generate(Table $table): string
    {
        $modelName = Str::studly(Str::singular($table->getName()));
        $tableName = $table->getName();
        $primaryKey = $table->getPrimaryKey();
        $translateAttributes = $table->getTranslatableColumns()
            ->map(fn($col) => $col->getName())
            ->toArray();

        $translatedAttributesCode = $this->generateTranslatedAttributes($translateAttributes);
        $castCode = $this->generateCasts($table);
        $relationshipsCode = $this->generateRelationships($table);

        return <<<PHP
<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Astrotomic\\Translatable\\Translatable;

class $modelName extends Model
{
    use Translatable;

    protected \$table = '$tableName';
    protected \$primaryKey = '$primaryKey';
$translatedAttributesCode$castCode$relationshipsCode
}
PHP;
    }

    /**
     * Generate translated attributes
     */
    protected function generateTranslatedAttributes(array $attributes): string
    {
        if (empty($attributes)) {
            return '';
        }

        $attributesList = implode("', '", $attributes);
        return <<<PHP

    /**
     * Attributes that are translatable.
     *
     * @var array
     */
    public \$translatedAttributes = ['$attributesList'];
PHP;
    }

    /**
     * Generate casts
     */
    protected function generateCasts(Table $table): string
    {
        $casts = [];
        $jsonColumns = [];

        foreach ($table->getColumns() as $column) {
            if ($column->getType() === 'json' || $column->getType() === 'jsonb') {
                $jsonColumns[] = $column->getName();
            } elseif ($column->getType() === 'boolean') {
                $casts[$column->getName()] = 'boolean';
            } elseif ($column->getType() === 'decimal' || $column->getType() === 'float') {
                $casts[$column->getName()] = 'float';
            } elseif ($column->getType() === 'integer' || $column->getType() === 'bigInteger') {
                $casts[$column->getName()] = 'integer';
            } elseif ($column->getType() === 'date' || $column->getType() === 'dateTime') {
                $casts[$column->getName()] = 'datetime';
            }
        }

        if (empty($casts) && empty($jsonColumns)) {
            return '';
        }

        $castLines = [];
        foreach ($casts as $attribute => $cast) {
            $castLines[] = "        '$attribute' => '$cast',";
        }

        foreach ($jsonColumns as $column) {
            $castLines[] = "        '$column' => 'json',";
        }

        $castsContent = implode("\n", $castLines);

        return <<<PHP

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected \$casts = [
$castsContent
    ];
PHP;
    }

    /**
     * Generate relationships
     */
    protected function generateRelationships(Table $table): string
    {
        $code = '';

        // Find foreign keys and generate relationships
        foreach ($table->getColumns() as $column) {
            if ($column->isForeignKey() && $column->getForeignTable()) {
                $relationshipName = Str::camel(Str::singular($column->getForeignTable()));
                $relatedModel = Str::studly(Str::singular($column->getForeignTable()));

                $code .= <<<PHP

    /**
     * Get the related {$relatedModel}.
     */
    public function {$relationshipName}()
    {
        return \$this->belongsTo({$relatedModel}::class, '{$column->getName()}');
    }
PHP;
            }
        }

        return $code;
    }
}
