<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-8">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-slate-900 mb-2">
                📚 Translatable Migration Builder
            </h1>
            <p class="text-lg text-slate-600">
                Create Laravel migrations with built-in translatable field support
            </p>
        </div>

        <!-- Progress Indicator -->
        <div class="mb-8 flex justify-center space-x-2">
            @foreach(['basic' => 'Basic Info', 'columns' => 'Columns', 'preview' => 'Preview'] as $step => $label)
                <button
                    wire:click="goToStep('{{ $step }}')"
                    @class([
                        'px-6 py-2 rounded-lg font-semibold transition-all',
                        'bg-indigo-600 text-white shadow-lg' => $currentStep === $step,
                        'bg-slate-200 text-slate-600 hover:bg-slate-300' => $currentStep !== $step,
                    ])
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        @if($currentStep === 'basic')
            <!-- Basic Information Step -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
                <h2 class="text-2xl font-bold text-slate-900 mb-6">Table Configuration</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Table Name -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Table Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model.live="tableName"
                            placeholder="e.g., products, posts"
                            class="w-full px-4 py-2 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none"
                        />
                        @error('tableName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Primary Key -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Primary Key
                        </label>
                        <input
                            type="text"
                            wire:model.live="primaryKey"
                            placeholder="e.g., id"
                            class="w-full px-4 py-2 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none"
                        />
                    </div>

                    <!-- Primary Key Type -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Primary Key Type
                        </label>
                        <select wire:model.live="primaryKeyType" class="w-full px-4 py-2 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none">
                            <option value="id">ID (Auto Increment)</option>
                            <option value="bigId">Big ID (64-bit Auto Increment)</option>
                            <option value="uuid">UUID</option>
                        </select>
                    </div>

                    <!-- Engine -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Database Engine
                        </label>
                        <select wire:model.live="engine" class="w-full px-4 py-2 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none">
                            <option value="">Default</option>
                            <option value="InnoDB">InnoDB</option>
                            <option value="MyISAM">MyISAM</option>
                        </select>
                    </div>

                    <!-- Charset -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Charset
                        </label>
                        <select wire:model.live="charset" class="w-full px-4 py-2 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none">
                            <option value="utf8mb4">UTF8MB4</option>
                            <option value="utf8">UTF8</option>
                            <option value="latin1">Latin1</option>
                        </select>
                    </div>

                    <!-- Collation -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            Collation
                        </label>
                        <input
                            type="text"
                            wire:model.live="collation"
                            placeholder="e.g., utf8mb4_unicode_ci"
                            class="w-full px-4 py-2 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none"
                        />
                    </div>
                </div>

                <!-- Custom Options -->
                <div class="border-t-2 border-slate-200 pt-6 mb-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Additional Options</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Timestamps -->
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="timestamps"
                                class="w-5 h-5 text-indigo-600 rounded"
                            />
                            <span class="text-slate-700">Add Timestamps</span>
                        </label>

                        <!-- Soft Deletes -->
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="softDeletes"
                                class="w-5 h-5 text-indigo-600 rounded"
                            />
                            <span class="text-slate-700">Add Soft Deletes</span>
                        </label>
                    </div>
                </div>

                <!-- Translation Table Customization -->
                <div class="border-t-2 border-slate-200 pt-6">
                    <h3 class="text-lg font-semibold text-slate-900 mb-4">Translation Table Customization</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Translation Table Name
                            </label>
                            <input
                                type="text"
                                wire:model.live="translationTableName"
                                placeholder="{{ $tableName }}_translations"
                                class="w-full px-4 py-2 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                Foreign Key Name
                            </label>
                            <input
                                type="text"
                                wire:model.live="translationForeignKeyName"
                                placeholder="{{ strtolower($tableName) }}_id"
                                class="w-full px-4 py-2 border-2 border-slate-200 rounded-lg focus:border-indigo-500 focus:outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="mt-8 flex justify-between">
                    <button
                        wire:click="resetBuilder"
                        class="px-6 py-3 bg-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-300 transition-colors"
                    >
                        Reset
                    </button>
                    <button
                        wire:click="goToStep('columns')"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition-colors"
                    >
                        Next: Add Columns →
                    </button>
                </div>
            </div>
        @endif

        @if($currentStep === 'columns')
            <!-- Columns Step -->
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-slate-900">Table Columns</h2>
                    <button
                        wire:click="addColumn"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors"
                    >
                        + Add Column
                    </button>
                </div>

                @if(count($columns) === 0)
                    <div class="text-center py-12 bg-slate-50 rounded-lg">
                        <p class="text-slate-500 text-lg mb-4">No columns added yet</p>
                        <button
                            wire:click="addColumn"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700"
                        >
                            Add First Column
                        </button>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($columns as $id => $column)
                            <div class="border-2 border-slate-200 rounded-lg p-6 bg-slate-50 hover:bg-slate-100 transition-colors">
                                <div class="flex justify-between items-start mb-4">
                                    <h4 class="text-lg font-semibold text-slate-900">Column {{ $loop->iteration }}</h4>
                                    <button
                                        wire:click="removeColumn({{ $id }})"
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm font-semibold"
                                    >
                                        Remove
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <!-- Column Name -->
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            Name <span class="text-red-500">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            wire:model="columns.{{ $id }}.name"
                                            placeholder="e.g., title, description"
                                            class="w-full px-3 py-2 border-2 border-slate-200 rounded focus:border-indigo-500 focus:outline-none"
                                        />
                                    </div>

                                    <!-- Type -->
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            Type
                                        </label>
                                        <select wire:model.live="columns.{{ $id }}.type" class="w-full px-3 py-2 border-2 border-slate-200 rounded focus:border-indigo-500 focus:outline-none">
                                            @foreach($columnTypes as $type)
                                                <option value="{{ $type }}">{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Length (for string) -->
                                    @if($column['type'] === 'string')
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                                Length
                                            </label>
                                            <input
                                                type="number"
                                                wire:model="columns.{{ $id }}.length"
                                                placeholder="255"
                                                class="w-full px-3 py-2 border-2 border-slate-200 rounded focus:border-indigo-500 focus:outline-none"
                                            />
                                        </div>
                                    @elseif($column['type'] === 'decimal')
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                                Precision
                                            </label>
                                            <input
                                                type="number"
                                                wire:model="columns.{{ $id }}.precision"
                                                placeholder="8"
                                                class="w-full px-3 py-2 border-2 border-slate-200 rounded focus:border-indigo-500 focus:outline-none"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-2">
                                                Scale
                                            </label>
                                            <input
                                                type="number"
                                                wire:model="columns.{{ $id }}.scale"
                                                placeholder="2"
                                                class="w-full px-3 py-2 border-2 border-slate-200 rounded focus:border-indigo-500 focus:outline-none"
                                            />
                                        </div>
                                    @endif

                                    <!-- Nullable -->
                                    <div class="flex items-end">
                                        <label class="flex items-center space-x-2 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                wire:model.live="columns.{{ $id }}.nullable"
                                                class="w-4 h-4 text-indigo-600 rounded"
                                            />
                                            <span class="text-sm font-semibold text-slate-700">Nullable</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                                    <!-- Index Type -->
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                                            Index
                                        </label>
                                        <select wire:model.live="columns.{{ $id }}.indexType" class="w-full px-3 py-2 border-2 border-slate-200 rounded focus:border-indigo-500 focus:outline-none">
                                            <option value="">None</option>
                                            <option value="index">Index</option>
                                            <option value="unique">Unique</option>
                                            <option value="primary">Primary</option>
                                        </select>
                                    </div>

                                    <!-- Default Value -->
                                    <div>
                                        <label class="flex items-center space-x-2 cursor-pointer mb-2">
                                            <input
                                                type="checkbox"
                                                wire:model.live="columns.{{ $id }}.hasDefault"
                                                class="w-4 h-4 text-indigo-600 rounded"
                                            />
                                            <span class="text-sm font-semibold text-slate-700">Default Value</span>
                                        </label>
                                        @if($column['hasDefault'])
                                            <input
                                                type="text"
                                                wire:model.live="columns.{{ $id }}.default"
                                                placeholder="Default value"
                                                class="w-full px-3 py-2 border-2 border-slate-200 rounded focus:border-indigo-500 focus:outline-none"
                                            />
                                        @endif
                                    </div>

                                    <!-- Foreign Key -->
                                    <div>
                                        <label class="flex items-center space-x-2 cursor-pointer mb-2">
                                            <input
                                                type="checkbox"
                                                wire:model.live="columns.{{ $id }}.isForeignKey"
                                                class="w-4 h-4 text-indigo-600 rounded"
                                            />
                                            <span class="text-sm font-semibold text-slate-700">Foreign Key</span>
                                        </label>
                                        @if($column['isForeignKey'])
                                            <input
                                                type="text"
                                                wire:model.live="columns.{{ $id }}.foreignTable"
                                                placeholder="Table name"
                                                class="w-full px-3 py-2 border-2 border-slate-200 rounded focus:border-indigo-500 focus:outline-none"
                                            />
                                        @endif
                                    </div>

                                    <!-- Translatable -->
                                    <div class="flex items-end">
                                        <label class="flex items-center space-x-2 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                wire:model.live="columns.{{ $id }}.translatable"
                                                class="w-4 h-4 text-indigo-600 rounded"
                                            />
                                            <span class="text-sm font-semibold text-slate-700">Translatable</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Navigation -->
                <div class="mt-8 flex justify-between">
                    <button
                        wire:click="goToStep('basic')"
                        class="px-6 py-3 bg-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-300 transition-colors"
                    >
                        ← Back
                    </button>
                    <button
                        wire:click="goToStep('preview')"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition-colors"
                    >
                        Next: Preview →
                    </button>
                </div>
            </div>
        @endif

        @if($currentStep === 'preview')
            <!-- Preview Step -->
            <div class="space-y-8">
                <!-- Migration Preview -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-slate-900">Migration Preview</h2>
                        <button
                            wire:click="copyToClipboard('migration')"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                        >
                            📋 Copy Code
                        </button>
                    </div>
                    <pre class="bg-slate-900 text-green-400 p-6 rounded-lg overflow-x-auto text-sm"><code>{{ $previewMigrationCode }}</code></pre>
                </div>

                <!-- Model Preview -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-slate-900">Model Preview</h2>
                        <button
                            wire:click="copyToClipboard('model')"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                        >
                            📋 Copy Code
                        </button>
                    </div>
                    <pre class="bg-slate-900 text-green-400 p-6 rounded-lg overflow-x-auto text-sm"><code>{{ $previewModelCode }}</code></pre>
                </div>

                <!-- Navigation -->
                <div class="flex justify-between">
                    <button
                        wire:click="goToStep('columns')"
                        class="px-6 py-3 bg-slate-200 text-slate-700 rounded-lg font-semibold hover:bg-slate-300 transition-colors"
                    >
                        ← Back
                    </button>
                    <div class="space-x-4">
                        <button
                            wire:click="generateInProject"
                            class="px-6 py-3 bg-emerald-700 text-white rounded-lg font-semibold hover:bg-emerald-800 transition-colors inline-block"
                        >
                            ⚙️ Generate In Project
                        </button>
                        <button
                            wire:click="downloadMigration"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors inline-block"
                        >
                            ⬇️ Download Migration
                        </button>
                        <button
                            wire:click="downloadModel"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors inline-block"
                        >
                            ⬇️ Download Model
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
