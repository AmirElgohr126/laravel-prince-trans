<?php

use Illuminate\Support\Facades\Route;
use Astrotomic\TranslatableMigrationBuilder\Http\Controllers\BuilderController;

Route::middleware(config('translatable-builder.middleware', ['web']))
    ->prefix(config('translatable-builder.route_prefix', 'translatable-builder'))
    ->name('translatable-builder.')
    ->group(function () {
        if (config('translatable-builder.enabled', true)) {
            Route::get('/', [BuilderController::class, 'index'])->name('index');
            Route::post('/download-migration', [BuilderController::class, 'downloadMigration'])->name('download-migration');
            Route::post('/download-model', [BuilderController::class, 'downloadModel'])->name('download-model');
        }
    });
