<?php

namespace Elgohr\Trans;

use Illuminate\Support\ServiceProvider;
use Elgohr\Trans\Commands\LaunchBuilderCommand;
use Elgohr\Trans\Livewire\BuilderComponent;
use Livewire\Livewire;

class TranslatableMigrationBuilderServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/translatable-builder.php',
            'translatable-builder'
        );

        $this->registerGenerators();
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishesConfig();
            $this->registersCommands();
        }

        if (class_exists(Livewire::class)) {
            Livewire::component('translatable-builder.builder', BuilderComponent::class);
        }

        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'translatable-builder');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Register generators
     */
    protected function registerGenerators(): void
    {
        $this->app->singleton(
            'translatable-builder.migration-generator',
            fn($app) => new Generators\MigrationGenerator()
        );

        $this->app->singleton(
            'translatable-builder.model-generator',
            fn($app) => new Generators\ModelGenerator()
        );
    }

    /**
     * Publish configuration
     */
    protected function publishesConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/translatable-builder.php' => config_path('translatable-builder.php'),
        ], 'translatable-builder-config');
    }

    /**
     * Register Artisan commands
     */
    protected function registersCommands(): void
    {
        $this->commands([
            LaunchBuilderCommand::class,
        ]);
    }
}
