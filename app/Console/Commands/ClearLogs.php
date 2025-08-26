<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear logs';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        File::cleanDirectory(storage_path('logs'));

        $this->info('All logs have been cleared.');
    }
}
