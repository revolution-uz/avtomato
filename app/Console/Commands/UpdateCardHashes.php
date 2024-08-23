<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\Company\Card;
use Illuminate\Console\Command;

class UpdateCardHashes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-card-hashes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Updating card hashes for all tenants...');

        Company::all()->runForEach(function ($company) {
            Card::query()->withTrashed()->get()->each(function ($card) {
                $card->hash = Card::hashing($card->number);
                $card->save();
            });

            $this->info("Card hashes updated for tenant: {$company->name}");
        });

        $this->info('Card hashes updated for all tenants.');
    }
}
