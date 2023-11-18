<?php

namespace App\Services\User;

use App\Enums\External\PlumTransactionResponseCodes;
use App\Enums\FeatureEnum;
use App\Exceptions\BalanceIsNotEnoughException;
use App\Models\Company\BalanceCharge;
use App\Models\Company\BalanceFund;
use App\Models\Company\Feature;
use Illuminate\Database\Eloquent\Model;

class BalanceService
{
    public function isBalanceEnoughForFeature(FeatureEnum $featureName): bool
    {
        /** @var Feature $feature */
        $feature = Feature::query()->where('name', $featureName)->firstOrFail();

        return $this->getBalance() >= $feature->chargeable_amount;
    }

    /**
     * @throws BalanceIsNotEnoughException
     */
    public function chargeAmount(FeatureEnum $featureName, Model $model): BalanceCharge
    {
        if (! $this->isBalanceEnoughForFeature($featureName)) {
            throw new BalanceIsNotEnoughException;
        }

        /** @var Feature $feature */
        $feature = Feature::query()->where('name', $featureName)->firstOrFail();

        return BalanceCharge::query()->create([
            'amount' => $feature->chargeable_amount,
            'feature_id' => $feature->id,
            'chargeable_id' => $model->getKey(),
            'chargeable_type' => get_class($model),
        ]);
    }

    public function getBalance(): int
    {
        return BalanceFund::query()
            ->where('plum_response_code', PlumTransactionResponseCodes::SUCCESS)
            ->sum('amount') - BalanceCharge::query()->sum('amount');
    }
}
