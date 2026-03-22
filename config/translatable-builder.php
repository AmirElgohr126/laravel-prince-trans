<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enable Translatable Migration Builder
    |--------------------------------------------------------------------------
    |
    | Set to false to disable the builder in production
    |
    */
    'enabled' => env('TRANSLATABLE_BUILDER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Builder Route Prefix
    |--------------------------------------------------------------------------
    |
    | The prefix for the builder routes
    |
    */
    'route_prefix' => 'translatable-builder',

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware to apply to builder routes
    |
    */
    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Default Migration Path
    |--------------------------------------------------------------------------
    |
    | Where generated migrations will be stored
    |
    */
    'migration_path' => 'database/migrations',

    /*
    |--------------------------------------------------------------------------
    | Default Model Path
    |--------------------------------------------------------------------------
    |
    | Where generated models will be stored
    |
    */
    'model_path' => 'app/Models',

    /*
    |--------------------------------------------------------------------------
    | Default Seeder Path
    |--------------------------------------------------------------------------
    |
    | Where generated seeders will be stored
    |
    */
    'seeder_path' => 'database/seeders',

    /*
    |--------------------------------------------------------------------------
    | Default Namespace for Generated Models
    |--------------------------------------------------------------------------
    |
    */
    'model_namespace' => 'App\\Models',

    /*
    |--------------------------------------------------------------------------
    | Default Column Types
    |--------------------------------------------------------------------------
    |
    | Available column types for the builder
    |
    */
    'column_types' => [
        'string',
        'text',
        'integer',
        'bigInteger',
        'decimal',
        'float',
        'boolean',
        'date',
        'dateTime',
        'time',
        'json',
        'jsonb',
        'uuid',
        'enum',
        'foreignId',
        'morphs',
    ],

    /*
    |--------------------------------------------------------------------------
    | Index Types
    |--------------------------------------------------------------------------
    |
    | Available index types
    |
    */
    'index_types' => [
        'index' => 'Index',
        'unique' => 'Unique',
        'primary' => 'Primary Key',
    ],
];
