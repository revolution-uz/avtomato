<?php

namespace App\Jobs;

use App\Clients\IcanClient;
use App\DTOs\Ican\IcanCreditDTO;
use App\Enums\Company\CustomerTypeEnum;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Tariff;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class SyncIcanCreditJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly int $page = 0)
    {
        $this->onQueue('sync-ican-credit');
    }

    /**
     * @throws Exception
     */
    public function handle(IcanClient $icanClient): void
    {
        $credits = $icanClient->getCredits();

        /** @var IcanCreditDTO $credit */
        foreach ($credits->data as $credit) {
            Installment::query()->updateOrCreate([
                'ican_id' => $credit->id,
            ])->update([
                'customer_id' => $this->createOrGetCustomer($credit->client_id, $credit),
                // 'branch_id' => $credit->, // todo: branch for ican
                'tariff_id' => $this->createOrGetTariff($credit->tariff_id, $credit),
                // 'admin_id' => $credit->id, // todo: admin for ican
                // 'type' => $credit->id, // ?
                'amount' => $credit->amount / 100,
                'initial_fee' => $credit->initial_payment / 100,
                'percent' => $credit->percent,
                'description' => $credit->comment,
                // 'sms_notify' => $credit->id, // ?
                // 'any_card_charge' => $credit->id, // ?
                // 'is_blacklist' => $credit->id, // ?
                // 'status' => $credit->id, // ?
                'period' => $credit->period,
                // 'plum_id' => $credit->id, // ?
                'canceled_at' => $credit->canceled_at,
                'cancellation_details' => $credit->cancel_reason,
                'starts_in' => $credit->activated_at,
                'paid_amount' => ($credit->amount - $credit->main_debt) / 100,
            ]);
        }

        if ($credits->meta->total > $this->page) {
            SyncIcanCreditJob::dispatch($this->page + 1);
        }
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return 'sync-ican-credit-' . tenant('id') . '-' . $this->page;
    }

    public function tags(): array
    {
        return [
            'tenant:' . tenant('id'),
        ];
    }

    /**
     * Get the cache driver for the unique job lock.
     */
    public function uniqueVia(): Repository
    {
        return Cache::driver(config('cache.default', 'redis'));
    }

    private function createOrGetTariff(?int $tariff_id, IcanCreditDTO $credit)
    {
        Tariff::query()->updateOrCreate([
            'ican_id' => $tariff_id,
        ])->update([
            'name' => $credit->tariff->name,
            'percent' => $credit->tariff->percent,
            'min_period' => $credit->tariff->min_period,
            'max_period' => $credit->tariff->max_period,
            'min_amount' => $credit->tariff->min_amount,
            'max_amount' => $credit->tariff->max_amount,
            // 'min_initial_fee' => $credit->tariff->min_initial_fee,
            // 'max_initial_fee' => $credit->tariff->max_initial_fee,
        ]);
    }

    private function createOrGetCustomer(?int $client_id, IcanCreditDTO $credit)
    {
        Customer::query()->updateOrCreate([
            'ican_id' => $client_id,
        ])->update([
            'pinfl' => $credit->person_main?->user_document->data->pinfl,
            'tin' => $credit->person_main?->user_document->data->inn,
            'passport_series' => $credit->person_main?->user_document->data->passport_series,
            'passport_number' => $credit->person_main?->user_document->data->passport_number,
            'passport_given_date' => $credit->person_main?->user_document->data->passport_issue_date,
            'passport_expiration_date' => $credit->person_main?->user_document->data->passport_expiry_date,
            // 'passport_given_place' => $credit->person_main?->user_document->data->passport_given_place,
            // 'passport_citizenship' => $credit->person_main?->user_document->data->passport_citizenship,
            'first_name' => $credit->person_main?->user_document->data->first_name,
            'last_name' => $credit->person_main?->user_document->data->last_name,
            'middle_name' => $credit->person_main?->user_document->data->middle_name,
            'birth_date' => $credit->person_main?->user_document->data->birth_date,
            // 'birth_place' => $credit->person_main?->user_document->data->birth_place,
            'gender' => $credit->person_main?->user_document->data->gender,
            'type' => CustomerTypeEnum::INSTALLMENT,
        ]);
    }
}
