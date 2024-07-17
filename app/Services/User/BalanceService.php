<?php

namespace App\Services\User;

use App\Clients\AtmosBalanceClient;
use App\DTOs\Atmos\Card\AtmosAttachCardResponseDTO;
use App\DTOs\Management\BalanceFundConfirmDTO;
use App\Enums\FeatureEnum;
use App\Exceptions\AtmosException;
use App\Exceptions\BalanceIsNotEnoughException;
use App\Models\Company\BalanceCharge;
use App\Models\Company\BalanceFund;
use App\Models\Company\Feature;
use Illuminate\Database\Eloquent\Model;

readonly class BalanceService
{
    public function __construct(
        private AtmosBalanceClient $atmosBalanceClient
    ) {}

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

    public function isBalanceEnoughForFeature(FeatureEnum $featureName): bool
    {
        /** @var Feature $feature */
        $feature = Feature::query()->where('name', $featureName)->firstOrFail();

        return $this->getBalance() >= $feature->chargeable_amount;
    }

    public function getBalance(): int
    {
        return BalanceFund::query()->sum('amount') - BalanceCharge::query()->sum('amount');
    }

    /**
     * @throws AtmosException
     */
    public function requestFillBalance(string $number, string $expiry): AtmosAttachCardResponseDTO
    {
        return $this->atmosBalanceClient->attachCard($number, $expiry);
    }

    /**
     * @throws AtmosException
     */
    public function fundBalance(BalanceFundConfirmDTO $balanceFundConfirmDTO): ?BalanceFund
    {
        $confirmCardResponseDTO = $this->atmosBalanceClient->confirmCard($balanceFundConfirmDTO->transaction_id, $balanceFundConfirmDTO->otp);

        if (! $confirmCardResponseDTO->result->isSucces()) {
            return null;
        }

        $createPayResponseDTO = $this->atmosBalanceClient->createPayment(
            'Fill balance',
            $balanceFundConfirmDTO->amount,
            'User is going to fill his own balance'
        );

        if (! $createPayResponseDTO->result->isSucces()) {
            return null;
        }

        $preConfirmPayResponseDTO = $this->atmosBalanceClient->preConfirmPayment($confirmCardResponseDTO->data->card_token, $createPayResponseDTO->transaction_id);

        if (! $preConfirmPayResponseDTO->result->isSucces()) {
            return null;
        }

        $confirmPayResponseDTO = $this->atmosBalanceClient->confirmPayment($createPayResponseDTO->transaction_id);

        if (! $confirmPayResponseDTO->result->isSucces()) {
            return null;
        }

        $balanceFund = new BalanceFund;
        $balanceFund->amount = $balanceFundConfirmDTO->amount;
        $balanceFund->card_number = $balanceFundConfirmDTO->number;
        $balanceFund->phone_number = $balanceFundConfirmDTO->phone;
        $balanceFund->external_transaction_id = $confirmPayResponseDTO->store_transaction->success_trans_id;
        $balanceFund->terminal_id = $confirmPayResponseDTO->store_transaction->terminal_id;
        if ($balanceFund->save()) {
            return $balanceFund;
        }

        $this->atmosBalanceClient->removeCard($confirmCardResponseDTO->data->card_token);

        return null;
    }
}
