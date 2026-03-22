<?php

namespace Astrotomic\TranslatableMigrationBuilder\Generators;

use Astrotomic\TranslatableMigrationBuilder\Builders\Table;
use Astrotomic\TranslatableMigrationBuilder\Builders\Column;
use Illuminate\Support\Str;

class MigrationGenerator
{
    /**
     * Generate migration code
     */
    public function generate(Table $table): string
    {
        $className = $this->generateClassName($table->getName());
        $timestamp = now()->format('Y_m_d_His');
        $filename = $timestamp . '_create_' . strtolower($table->getName()) . '_table.php';

        $code = $this->generateMigrationCode($table, $className);

        return $code;
    }

    /**
     * Generate the full migration code
     */
    public function generateMigrationCode(Table $table, string $className): string
    {
        $mainTableCode = $this->generateMainTableSchema($table);
        $translationTableCode = '';

        if ($table->getTranslatableColumns()->count() > 0) {
            $translationTableCode = "\n\n" . $this->generateTranslationTableSchema($table);
        }

        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
$mainTableCode$translationTableCode
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$table->getTranslationTableName()}');
        Schema::dropIfExists('{$table->getName()}');
    }
};
PHP;
    }

    /**
     * Generate main table schema
     */
    protected function generateMainTableSchema(Table $table): string
    {
        $columns = $table->getNonTranslatableColumns();
        $columnsCode = $this->generateColumnsCode($columns, $table, true, true);
        $schemaOptions = $this->generateSchemaOptions($table);

        return <<<PHP
        Schema::create('{$table->getName()}', function (Blueprint \$table) {
$columnsCode$schemaOptions        });
PHP;
    }

    /**
     * Generate translation table schema
     */
    protected function generateTranslationTableSchema(Table $table): string
    {
        $translateColumns = $table->getTranslatableColumns();
        $foreignKeyName = $table->getTranslationForeignKeyName();
        $primaryKeyName = $table->getPrimaryKey();

        $columnsCode = $this->generateColumnsCode($translateColumns, $table, false, false);

        if ($table->getPrimaryKeyType() === 'uuid') {
            $columnsCode .= "            \$table->uuid('$foreignKeyName');\n";
        } else {
            $columnsCode .= "            \$table->unsignedBigInteger('$foreignKeyName');\n";
        }

        // Add translation-specific columns
        $columnsCode .= <<<PHP
            \$table->string('locale')->index();
            \$table->unique(['$foreignKeyName', 'locale']);

PHP;

        // Foreign key
        $columnsCode .= <<<PHP
            \$table->foreign('$foreignKeyName')
                ->references('$primaryKeyName')
                ->on('{$table->getName()}')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

PHP;

        return <<<PHP
        Schema::create('{$table->getTranslationTableName()}', function (Blueprint \$table) {
            \$table->id('id');
$columnsCode        });
PHP;
    }

    /**
     * Generate columns code
     */
    protected function generateColumnsCode($columns, Table $table, bool $includePrimaryKey = true, bool $includeMetaColumns = true): string
    {
        $code = '';

        // Add primary key
        if ($includePrimaryKey) {
            if ($table->getPrimaryKeyType() === 'bigId') {
                $code .= "            \$table->bigIncrements('{$table->getPrimaryKey()}');\n";
            } elseif ($table->getPrimaryKeyType() === 'uuid') {
                $code .= "            \$table->uuid('{$table->getPrimaryKey()}')->primary();\n";
            } else {
                $code .= "            \$table->id('{$table->getPrimaryKey()}');\n";
            }
        }

        // Add soft deletes if needed
        if ($includeMetaColumns && $table->hasSoftDeletes() && $table->getNonTranslatableColumns()->count() > 0) {
            $code .= "            \$table->softDeletes();\n";
        }

        // Add columns
        foreach ($columns as $column) {
            $code .= $this->generateColumnCode($column, $table);
        }

        // Add timestamps if needed
        if ($includeMetaColumns && $table->hasTimestamps() && $table->getNonTranslatableColumns()->count() > 0) {
            $code .= "            \$table->timestamps();\n";
        }

        return $code;
    }

    /**
     * Generate single column code
     */
    protected function generateColumnCode(Column $column, Table $table): string
    {
        $columnName = $column->getName();
        $columnType = $column->getType();

        $code = "            \$table->$columnType('$columnName'";

        // Handle parameters
        if ($columnType === 'string' && $column->getLength()) {
            $code .= ", {$column->getLength()}";
        } elseif ($columnType === 'decimal' && $column->getPrecision()) {
            $code .= ", {$column->getPrecision()}, {$column->getScale()}";
        } elseif ($columnType === 'enum' && $column->getModifiers()) {
            $modifiers = implode("', '", $column->getModifiers());
            $code .= ", ['$modifiers']";
        } elseif ($columnType === 'foreignId') {
            // foreignId doesn't need length
            $code = str_replace("'$columnName'", "$columnName", $code);
        }

        $code .= ")";

        // Add modifiers
        if ($column->isNullable()) {
            $code .= "\n                ->nullable()";
        }

        if ($column->hasDefault()) {
            $default = $column->getDefault();
            if (is_bool($default)) {
                $default = $default ? 'true' : 'false';
            } elseif (is_string($default) && !in_array($default, ['true', 'false', 'null'])) {
                $default = "'" . $default . "'";
            }
            $code .= "\n                ->default($default)";
        }

        // Handle indexes
        if ($column->getIndexType() === 'unique') {
            $code .= "\n                ->unique()";
        } elseif ($column->getIndexType() === 'index') {
            $code .= "\n                ->index()";
        }

        // Handle foreign keys
        if ($column->isForeignKey() && $column->getForeignTable()) {
            $code = str_replace("->index()", "", $code);
            $code = str_replace("->unique()", "", $code);
            $code .= "\n                ->constrained('{$column->getForeignTable()}', '{$column->getForeignColumn()}')\n";
            $code .= "                ->onDelete('{$column->getOnDelete()}')\n";
            $code .= "                ->onUpdate('{$column->getOnUpdate()}')";
        }

        $code .= ";\n";

        return $code;
    }

    /**
     * Generate schema options
     */
    protected function generateSchemaOptions(Table $table): string
    {
        $options = [];

        if ($table->getEngine()) {
            $options[] = "\$table->engine = '{$table->getEngine()}';";
        }

        if ($table->getCharset()) {
            $options[] = "\$table->charset = '{$table->getCharset()}';";
        }

        if ($table->getCollation()) {
            $options[] = "\$table->collation = '{$table->getCollation()}';";
        }

        if (empty($options)) {
            return '';
        }

        return "            " . implode("\n            ", $options) . "\n";
    }

    /**
     * Generate class name from table name
     */
    protected function generateClassName(string $tableName): string
    {
        return 'Create' . Str::studly($tableName) . 'Table';
    }

    /**
     * Get filename
     */
    public function getFilename(Table $table): string
    {
        $timestamp = now()->format('Y_m_d_His');
        return $timestamp . '_create_' . Str::snake($table->getName()) . '_table.php';
    }
}
