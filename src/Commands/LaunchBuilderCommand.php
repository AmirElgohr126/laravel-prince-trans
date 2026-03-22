<?php

namespace Astrotomic\TranslatableMigrationBuilder\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\URL;

class LaunchBuilderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translatable:builder
                            {--serve : Start a development server if not running}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Launch the Translatable Migration Builder UI';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $routePrefix = config('translatable-builder.route_prefix', 'translatable-builder');
        $url = route('translatable-builder.index');

        $this->info('🚀 Translatable Migration Builder is launching...');
        $this->newLine();
        $this->info("Visit: <fg=cyan>$url</>");
        $this->newLine();

        if ($this->option('serve')) {
            $this->info('Starting development server...');
            $this->call('serve');
        } else {
            $this->info('Tip: Use <fg=cyan>--serve</> flag to start a development server automatically');
            $this->newLine();
            $this->info('Example:');
            $this->info('<fg=cyan>  php artisan translatable:builder --serve</>');
        }

        return self::SUCCESS;
    }
}
