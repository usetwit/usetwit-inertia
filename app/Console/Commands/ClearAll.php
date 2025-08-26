<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clearall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all Laravel caches (config, route, view, and application cache)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Clearing all caches...');

        // Clear cache
        $this->call('cache:clear');
        $this->info('Application cache cleared.');

        // Clear config cache
        $this->call('config:clear');
        $this->info('Configuration cache cleared.');

        // Clear route cache
        $this->call('route:clear');
        $this->info('Route cache cleared.');

        // Clear view cache
        $this->call('view:clear');
        $this->info('Compiled views cleared.');

        // Clear logs
        $this->call('app:clear-logs');
        $this->info('Logs cleared.');

        $this->info('All caches cleared successfully! 🎉');

        return 0;
    }
}
