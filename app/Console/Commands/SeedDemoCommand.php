<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedDemoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrate:fresh --seed to reset database and seed with demo data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Running migrate:fresh --seed...');

        $this->call('migrate:fresh', [
            '--seed' => true,
        ]);

        $this->info('Demo data seeded successfully!');
    }
}

