<?php

namespace App\Console\Commands\DB;

use Exception;
use Illuminate\Console\Command;

class RefreshDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'refresh:db {--seed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'refresh:db';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        try {
            $this->call('migrate:reset');
            $this->call('migrate');
            if ($this->option('seed')) {
                $this->call('db:seed');
            }
        } catch (Exception $exception) {
            dd($exception);
        }

        return 0;
    }
}
