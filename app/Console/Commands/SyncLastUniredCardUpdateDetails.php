<?php

namespace App\Console\Commands;

use App\Clients\UniredClient;
use App\DTOs\Unired\Client\UniredPinflCardUpdateResponseDTO;
use App\Models\CentralCustomer;
use App\Services\UniredService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Command\Command as CommandAlias;

class SyncLastUniredCardUpdateDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:last-unired-card-update-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync last Unired card update details';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $service = new UniredService(new UniredClient);
        /** @var CentralCustomer $customer */
        foreach (CentralCustomer::all() as $customer) {
            /** @var Collection<int,UniredPinflCardUpdateResponseDTO> $cardUpdates */
            $cardUpdates = $service->getCardUpdates($customer->pinfl);

            $this->info('----------------------------');
            $this->info('Current customer: ' . $customer->id);
            $this->info('Uzcard searched at: ' . $customer->unired_uzcard_searched_at);
            $this->info('Humo searched at: ' . $customer->unired_humo_searched_at);
            $this->info('Unired card updates count: ' . $cardUpdates->count());
            $customer->unired_uzcard_searched_at = null;
            $customer->unired_humo_searched_at = null;

            if ($cardUpdates->isEmpty()) {
                $this->info('No card updates for customer ' . $customer->id);
                $customer->save();

                continue;
            }

            foreach ($cardUpdates as $cardUpdate) {
                if ($cardUpdate->status === 'success' && $cardUpdate->cards_count > 0) {
                    if ($cardUpdate->type === 'uzcard') {
                        if (is_null($customer->unired_uzcard_searched_at) || $customer->unired_uzcard_searched_at <= Carbon::parse($cardUpdate->processed_at)) {
                            $customer->unired_uzcard_searched_at = Carbon::parse($cardUpdate->processed_at);
                        }
                    } elseif ($cardUpdate->type === 'humo') {
                        if (is_null($customer->unired_humo_searched_at) || $customer->unired_humo_searched_at <= Carbon::parse($cardUpdate->processed_at)) {
                            $customer->unired_humo_searched_at = Carbon::parse($cardUpdate->processed_at);
                        }
                    }
                }
            }

            $this->info('Uzcard searched at (updated): ' . $customer->unired_uzcard_searched_at);
            $this->info('Humo searched at (updated): ' . $customer->unired_humo_searched_at);
            $customer->save();
        }

        return CommandAlias::SUCCESS;
    }
}
