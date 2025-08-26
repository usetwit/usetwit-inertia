<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RefreshAndSeedCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrate:refresh and db:seed';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Running migrate:refresh...');
        $this->call('migrate:refresh');

        $this->info('Running db:seed...');
        $this->call('db:seed');

        $this->info('Done!');

        return 0;
    }
}
