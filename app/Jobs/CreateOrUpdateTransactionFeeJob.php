<?php

namespace App\Jobs;

use App\Enums\FeatureEnum;
use App\Exceptions\UniredException;
use App\Models\Company\Feature;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use App\Services\User\BalanceService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class CreateOrUpdateTransactionFeeJob implements ShouldBeUniqueUntilProcessing, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const JOB_ID = 'create-or-update-transaction-fee-job';

    public function __construct(private readonly int $transactionId)
    {
        $this->onQueue(self::JOB_ID);
    }

    /**
     * @throws Exception
     */
    public function handle(BalanceService $balanceService): void
    {

        $transaction = Transaction::query()->findOrFail($this->transactionId);

        try {

            if ($featureEnum = TransactionType::getFeature($transaction->transaction_type_id)) {
                $feature = Feature::query()
                    ->where('feature', $featureEnum)
                    ->firstOrFail();

                $comission = $transaction->isHumo() ? $feature->humo_percent : $feature->uzcard_percent;
                if ($comission && ($amount = $transaction->amount * $comission) > 0) {
                    $balanceService->chargeAmount(FeatureEnum::UNIRED_PAY, $transaction, $amount);
                }
            }
        } catch (UniredException $e) {
            return;
        }
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return self::JOB_ID . '-' . tenant('id') . '-' . $this->transactionId;
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
}
