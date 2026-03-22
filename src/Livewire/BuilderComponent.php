<?php

namespace Astrotomic\TranslatableMigrationBuilder\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Astrotomic\TranslatableMigrationBuilder\Builders\Table;
use Astrotomic\TranslatableMigrationBuilder\Builders\Column;
use Astrotomic\TranslatableMigrationBuilder\Generators\MigrationGenerator;
use Astrotomic\TranslatableMigrationBuilder\Generators\ModelGenerator;

class BuilderComponent extends Component
{
    public $tableName = '';
    public $primaryKey = 'id';
    public $primaryKeyType = 'id';
    public $timestamps = false;
    public $softDeletes = false;
    public $engine = 'InnoDB';
    public $charset = 'utf8mb4';
    public $collation = 'utf8mb4_unicode_ci';
    public $translationTableName = '';
    public $translationForeignKeyName = '';

    public $columns = [];
    public $columnIdCounter = 0;

    public $currentStep = 'basic'; // basic, columns, preview, export
    public $previewMigrationCode = '';
    public $previewModelCode = '';

    public $columnTypes = [];
    public $indexTypes = [];

    protected $rules = [
        'tableName' => 'required|string|regex:/^[a-z_][a-z0-9_]*$/',
        'primaryKey' => 'required|string',
    ];

    public function mount()
    {
        $this->columnTypes = config('translatable-builder.column_types', [
            'string', 'text', 'integer', 'bigInteger', 'decimal', 'float',
            'boolean', 'date', 'dateTime', 'time', 'json', 'jsonb', 'uuid', 'enum'
        ]);

        $this->indexTypes = config('translatable-builder.index_types', [
            'index' => 'Index',
            'unique' => 'Unique',
            'primary' => 'Primary Key',
        ]);
    }

    public function render()
    {
        return view('translatable-builder::livewire.builder')
            ->with([
                'columnTypes' => $this->columnTypes,
                'indexTypes' => $this->indexTypes,
                'currentStep' => $this->currentStep,
            ]);
    }

    /**
     * Add a new column
     */
    public function addColumn()
    {
        $id = $this->columnIdCounter++;
        $this->columns[$id] = [
            'id' => $id,
            'name' => '',
            'type' => 'string',
            'length' => null,
            'precision' => null,
            'scale' => null,
            'nullable' => false,
            'default' => null,
            'hasDefault' => false,
            'indexType' => null,
            'isForeignKey' => false,
            'foreignTable' => '',
            'foreignColumn' => 'id',
            'onDelete' => 'cascade',
            'onUpdate' => 'cascade',
            'translatable' => false,
            'modifiers' => [],
        ];
        $this->dispatch('column-added', id: $id);
    }

    /**
     * Remove a column
     */
    public function removeColumn($id)
    {
        unset($this->columns[$id]);
        $this->dispatch('column-removed', id: $id);
    }

    /**
     * Update column property
     */
    public function updateColumn($id, $property, $value)
    {
        if (isset($this->columns[$id])) {
            // Handle special cases
            if ($property === 'type' && in_array($value, ['string', 'decimal'])) {
                // Reset length/precision when changing type
                $this->columns[$id]['length'] = null;
                $this->columns[$id]['precision'] = null;
                $this->columns[$id]['scale'] = null;
            }

            $this->columns[$id][$property] = $value;
            $this->dispatch('column-updated', id: $id, property: $property);
        }
    }

    /**
     * Reorder columns
     */
    public function reorderColumns($newOrder)
    {
        $reordered = [];
        foreach ($newOrder as $id) {
            if (isset($this->columns[$id])) {
                $reordered[$id] = $this->columns[$id];
            }
        }
        $this->columns = $reordered;
    }

    /**
     * Go to step
     */
    public function goToStep($step)
    {
        if ($step === 'basic' || $step === 'columns') {
            if ($this->validateTableConfig()) {
                $this->currentStep = $step;
            }

            return;
        }

        if ($this->validateForGeneration()) {
            if ($step === 'preview') {
                $this->generatePreview();
            }

            $this->currentStep = $step;
        }
    }

    /**
     * Validate table-level configuration before editing columns
     */
    protected function validateTableConfig(): bool
    {
        if (empty($this->tableName)) {
            $this->dispatch('notify', message: 'Table name is required', type: 'error');
            return false;
        }

        return true;
    }

    /**
     * Validate data required for preview/export generation
     */
    protected function validateForGeneration(): bool
    {
        if (!$this->validateTableConfig()) {
            return false;
        }

        if (count($this->columns) === 0) {
            $this->dispatch('notify', message: 'Please add at least one column', type: 'error');
            return false;
        }

        return true;
    }

    /**
     * Generate preview
     */
    protected function generatePreview()
    {
        try {
            $table = $this->buildTableObject();
            $migrationGenerator = new MigrationGenerator();
            $modelGenerator = new ModelGenerator();

            $this->previewMigrationCode = $migrationGenerator->generate($table);
            $this->previewModelCode = $modelGenerator->generate($table);
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Error generating preview: ' . $e->getMessage(), type: 'error');
        }
    }

    /**
     * Build Table object from Livewire data
     */
    protected function buildTableObject(): Table
    {
        $table = new Table($this->tableName);

        $table->setPrimaryKey($this->primaryKey);
        $table->setPrimaryKeyType($this->primaryKeyType);
        $table->setTimestamps($this->timestamps);
        $table->setSoftDeletes($this->softDeletes);
        $table->setEngine($this->engine ?: null);
        $table->setCharset($this->charset ?: null);
        $table->setCollation($this->collation ?: null);

        if ($this->translationTableName) {
            $table->setTranslationTableName($this->translationTableName);
        }
        if ($this->translationForeignKeyName) {
            $table->setTranslationForeignKeyName($this->translationForeignKeyName);
        }

        // Add columns
        foreach ($this->columns as $columnData) {
            $column = Column::fromArray($columnData);
            $table->addColumn($column);
        }

        return $table;
    }

    /**
     * Download migration
     */
    public function downloadMigration()
    {
        try {
            $table = $this->buildTableObject();
            $generator = new MigrationGenerator();
            $filename = $generator->getFilename($table);
            $code = $generator->generate($table);

            return response()->streamDownload(
                function () use ($code) {
                    echo $code;
                },
                $filename,
                ['Content-Type' => 'text/plain']
            );
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Error downloading migration: ' . $e->getMessage(), type: 'error');
        }
    }

    /**
     * Download model
     */
    public function downloadModel()
    {
        try {
            $table = $this->buildTableObject();
            $generator = new ModelGenerator();
            $code = $generator->generate($table);
            $filename = ucfirst($this->tableName) . '.php';

            return response()->streamDownload(
                function () use ($code) {
                    echo $code;
                },
                $filename,
                ['Content-Type' => 'text/plain']
            );
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Error downloading model: ' . $e->getMessage(), type: 'error');
        }
    }

    /**
     * Generate migration and model scaffolding directly inside project files.
     */
    public function generateInProject(): void
    {
        if (!$this->validateForGeneration()) {
            return;
        }

        try {
            $table = $this->buildTableObject();
            $migrationGenerator = new MigrationGenerator();
            $migrationFilename = $migrationGenerator->getFilename($table);
            $migrationPath = base_path(trim(config('translatable-builder.migration_path', 'database/migrations'), '/\\') . DIRECTORY_SEPARATOR . $migrationFilename);

            File::ensureDirectoryExists(dirname($migrationPath));
            File::put($migrationPath, $migrationGenerator->generate($table));

            $modelName = Str::studly(Str::singular($table->getName()));
            $modelDirectory = app_path('Models' . DIRECTORY_SEPARATOR . $modelName);
            $repositoryDirectory = $modelDirectory . DIRECTORY_SEPARATOR . $modelName . 'EloquentRepository';

            File::ensureDirectoryExists($modelDirectory);
            File::ensureDirectoryExists($repositoryDirectory);

            $mainModelPath = $modelDirectory . DIRECTORY_SEPARATOR . $modelName . '.php';
            $translationModelPath = $modelDirectory . DIRECTORY_SEPARATOR . $modelName . 'Translation.php';
            $repositoryInterfacePath = $modelDirectory . DIRECTORY_SEPARATOR . $modelName . 'Repository.php';
            $repositoryClassName = 'Eloquent' . $modelName . 'Repository';
            $repositoryImplementationPath = $repositoryDirectory . DIRECTORY_SEPARATOR . $repositoryClassName . '.php';
            $seederFilename = $modelName . 'Seeder.php';
            $seederPath = base_path(trim(config('translatable-builder.seeder_path', 'database/seeders'), '/\\') . DIRECTORY_SEPARATOR . $seederFilename);

            File::put($mainModelPath, $this->generateMainModelClass($table, $modelName));
            File::put($translationModelPath, $this->generateTranslationModelClass($table, $modelName));
            File::put($repositoryInterfacePath, $this->generateRepositoryInterface($table, $modelName));
            File::put($repositoryImplementationPath, $this->generateRepositoryImplementation($table, $modelName));
            File::ensureDirectoryExists(dirname($seederPath));
            File::put($seederPath, $this->generateSeederClass($table, $modelName));

            $this->dispatch('notify', message: 'Files and seeder generated successfully inside your project.', type: 'success');
        } catch (\Throwable $throwable) {
            $this->dispatch('notify', message: 'Error generating project files: ' . $throwable->getMessage(), type: 'error');
        }
    }

    /**
     * Build seeder class content for database/seeders/{Model}Seeder.php
     */
    protected function generateSeederClass(Table $table, string $modelName): string
    {
        $primaryKey = $table->getPrimaryKey();
        $mainTableName = $table->getName();
        $translationTableName = $table->getTranslationTableName();
        $translationForeignKey = $table->getTranslationForeignKeyName();
        $hasTranslations = $table->getTranslatableColumns()->count() > 0;

        $mainDataLines = [];
        foreach ($table->getNonTranslatableColumns() as $column) {
            $mainDataLines[] = "            '{$column->getName()}' => {$this->getSeederValueByType($column->getType())},";
        }

        if ($table->hasTimestamps()) {
            $mainDataLines[] = "            'created_at' => now(),";
            $mainDataLines[] = "            'updated_at' => now(),";
        }

        $mainData = implode("\n", $mainDataLines);
        $translationBlock = '';

        if ($hasTranslations) {
            $translationDataLines = [];
            foreach ($table->getTranslatableColumns() as $column) {
                $translationDataLines[] = "                '{$column->getName()}' => 'Sample {$column->getName()}',";
            }

            $translationData = implode("\n", $translationDataLines);

            $translationBlock = <<<PHP

        DB::table('{$translationTableName}')->insert([
            [
                '{$translationForeignKey}' => \$recordId,
                'locale' => config('app.locale', 'en'),
$translationData
            ],
        ]);
PHP;
        }

        return <<<PHP
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class {$modelName}Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('{$mainTableName}')->insert([
$mainData
        ]);

        \$recordId = DB::getPdo()->lastInsertId();$translationBlock
    }
}
PHP;
    }

    /**
     * Map column type to a simple seed value.
     */
    protected function getSeederValueByType(string $type): string
    {
        return match ($type) {
            'integer', 'bigInteger', 'foreignId' => '1',
            'decimal', 'float' => '0',
            'boolean' => 'true',
            'json', 'jsonb' => "'{}'",
            'date' => 'now()->toDateString()',
            'dateTime', 'time' => 'now()',
            default => "'Sample'",
        };
    }

    /**
     * Build main model class content for app/Models/{Model}/{Model}.php
     */
    protected function generateMainModelClass(Table $table, string $modelName): string
    {
        $namespace = 'App\\Models\\' . $modelName;
        $tableName = $table->getName();
        $primaryKey = $table->getPrimaryKey();
        $translationForeignKey = $table->getTranslationForeignKeyName();
        $translatedAttributes = $table->getTranslatableColumns()->map(fn(Column $column) => $column->getName())->values()->all();
        $translatedAttributesList = implode("', '", $translatedAttributes);
        $usesTranslatable = class_exists('Astrotomic\\Translatable\\Translatable');
        $hasTranslatedAttributes = count($translatedAttributes) > 0;

        $translatableImport = $usesTranslatable ? "use Astrotomic\\Translatable\\Translatable;\n" : '';
        $translatableTrait = $usesTranslatable ? "    use Translatable;\n\n" : '';
        $translatedAttributesBlock = $hasTranslatedAttributes
            ? "    public \$translatedAttributes = ['{$translatedAttributesList}'];\n\n"
            : '';

        return <<<PHP
<?php

namespace {$namespace};

use Illuminate\Database\Eloquent\Model;
{$translatableImport}use Illuminate\Database\Eloquent\Relations\HasMany;

class $modelName extends Model
{
{$translatableTrait}    protected \$table = '{$tableName}';
    protected \$primaryKey = '{$primaryKey}';

{$translatedAttributesBlock}    public function translations(): HasMany
    {
        return \$this->hasMany({$modelName}Translation::class, '{$translationForeignKey}');
    }
}
PHP;
    }

    /**
     * Build translation model class content for app/Models/{Model}/{Model}Translation.php
     */
    protected function generateTranslationModelClass(Table $table, string $modelName): string
    {
        $namespace = 'App\\Models\\' . $modelName;
        $translationTableName = $table->getTranslationTableName();
        $translationForeignKey = $table->getTranslationForeignKeyName();
        $relationMethod = $this->camelCaseModel($modelName);
        $fillable = $table->getTranslatableColumns()->map(fn(Column $column) => "'{$column->getName()}'")->values()->all();
        $fillable[] = "'locale'";
        $fillable[] = "'{$translationForeignKey}'";
        $fillableString = implode(', ', $fillable);

        return <<<PHP
<?php

namespace {$namespace};

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class {$modelName}Translation extends Model
{
    protected \$table = '{$translationTableName}';

    protected \$fillable = [{$fillableString}];

    public function {$relationMethod}(): BelongsTo
    {
        return \$this->belongsTo({$modelName}::class, '{$translationForeignKey}');
    }
}
PHP;
    }

    /**
     * Build repository interface content for app/Models/{Model}/{Model}Repository.php
     */
    protected function generateRepositoryInterface(Table $table, string $modelName): string
    {
        $namespace = 'App\\Models\\' . $modelName;

        return <<<PHP
<?php

namespace {$namespace};

interface {$modelName}Repository
{
    public function adminIndex();

    public function adminShow(\$id);

    public function adminCreate(\$data);

    public function adminUpdate(\$model, \$data);

    public function adminDelete(\$model);

    public function adminUpdateMany(\$ids, \$data);

    public function adminDeleteMany(\$ids);
}
PHP;
    }

    /**
     * Build repository implementation content for app/Models/{Model}/{Model}EloquentRepository/{Model}EloquentRepository.php
     */
    protected function generateRepositoryImplementation(Table $table, string $modelName): string
    {
        $repositoryClassName = 'Eloquent' . $modelName . 'Repository';
        $repositoryNamespace = 'App\\Models\\' . $modelName . '\\' . $modelName . 'EloquentRepository';
        $repositoryImport = 'App\\Models\\' . $modelName . '\\' . $modelName . 'Repository';
        $baseRepositoryImport = 'App\\Repositories\\EloquentBaseRepository';

        return <<<PHP
<?php

namespace {$repositoryNamespace};

use {$repositoryImport};
use {$baseRepositoryImport};

class {$repositoryClassName} extends EloquentBaseRepository implements {$modelName}Repository
{
    public function adminCreate(\$data)
    {
        return \$this->model::create(\$data);
    }

    public function adminUpdate(\$model, \$data)
    {
        \$model->fill(\$data);
        \$model->save();

        return \$model;
    }

    public function adminDelete(\$model)
    {
        return \$model->delete();
    }

    public function adminIndex()
    {
        return \$this->all('created_at', request('order_by', 'desc'))->get();
    }

    public function adminShow(\$id)
    {
        return \$this->model::findOrFail(\$id);
    }

    public function adminUpdateMany(\$ids, \$data)
    {
        return \$this->model::whereIn('id', \$ids)->update(\$data);
    }

    public function adminDeleteMany(\$ids)
    {
        return \$this->model::whereIn('id', \$ids)->delete();
    }
}
PHP;
    }

    /**
     * Convert model name to camelCase for relation methods.
     */
    protected function camelCaseModel(string $modelName): string
    {
        return Str::camel($modelName);
    }

    /**
     * Copy to clipboard
     */
    public function copyToClipboard($type)
    {
        if ($type === 'migration') {
            $this->dispatch('copy-migration', code: $this->previewMigrationCode);
        } elseif ($type === 'model') {
            $this->dispatch('copy-model', code: $this->previewModelCode);
        }
    }

    /**
     * Reset builder
     */
    public function resetBuilder()
    {
        $this->reset();
        $this->mount();
        $this->currentStep = 'basic';
    }
}
