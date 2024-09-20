<?php

namespace App\Services\User;

use App\Clients\AtmosBalanceClient;
use App\DTOs\Atmos\CardPay\AtmosCreatePaymentResponseDTO;
use App\DTOs\Management\BalanceFundConfirmDTO;
use App\Enums\Company\CompanyStatusEnum;
use App\Enums\FeatureEnum;
use App\Exceptions\AtmosException;
use App\Models\Company\BalanceCharge;
use App\Models\Company\BalanceFund;
use App\Models\Company\Feature;
use Illuminate\Database\Eloquent\Model;

readonly class BalanceService
{
    public function __construct(
        private AtmosBalanceClient $atmosBalanceClient
    ) {}

    public function chargeAmount(FeatureEnum $featureName, Model $model): BalanceCharge
    {
        $this->getBalance();

        /** @var Feature $feature */
        $feature = Feature::query()->where('name', $featureName)->firstOrFail();

        return BalanceCharge::query()->create([
            'amount' => $feature->chargeable_amount,
            'feature_id' => $feature->id,
            'chargeable_id' => $model->getKey(),
            'chargeable_type' => get_class($model),
        ]);
    }

    public function isBalanceEnoughForFeature(): bool
    {
        return $this->getBalance() > -100000;
    }

    public function getBalance(): int
    {
        $balance = BalanceFund::query()->sum('amount') - BalanceCharge::query()->sum('amount');

        if (tenant()->status == CompanyStatusEnum::ACTIVE and $balance <= -100000) {
            tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);
        } elseif (tenant()->status != CompanyStatusEnum::ACTIVE and $balance > -100000) {
            tenant()->updateOrFail(['status' => CompanyStatusEnum::ACTIVE]);
        }

        return $balance;
    }

    /**
     * @throws AtmosException
     */
    public function requestFillBalance(string $number, string $expiry, int $amount): AtmosCreatePaymentResponseDTO
    {
        $createPayResponseDTO = $this->atmosBalanceClient->createPayment(
            'Fill balance',
            $amount,
            'User is filling his own balance'
        );

        if (! $createPayResponseDTO->result->isSuccess()) {
            throw new AtmosException($createPayResponseDTO->result->description);
        }

        $preConfirmPayResponseDTO = $this->atmosBalanceClient->preConfirmPaymentWithOneTimeSms($createPayResponseDTO->transaction_id, $number, $expiry);

        if (! $preConfirmPayResponseDTO->result->isSuccess()) {
            throw new AtmosException($preConfirmPayResponseDTO->result->description);
        }

        return $createPayResponseDTO;
    }

    public function fundBalance(BalanceFundConfirmDTO $balanceFundConfirmDTO): BalanceFund
    {
        $confirmPayResponseDTO = $this->atmosBalanceClient->confirmPayment(
            $balanceFundConfirmDTO->transaction_id,
            $balanceFundConfirmDTO->otp
        );

        if (! $confirmPayResponseDTO->result->isSuccess()) {
            throw new AtmosException($confirmPayResponseDTO->result->description);
        }

        $balanceFund = new BalanceFund;
        $balanceFund->amount = $confirmPayResponseDTO->store_transaction->amount / 100; // soum
        $balanceFund->card_number = $balanceFundConfirmDTO->number;
        $balanceFund->phone_number = $balanceFundConfirmDTO->phone;
        $balanceFund->external_transaction_id = $confirmPayResponseDTO->store_transaction->success_trans_id;
        $balanceFund->terminal_id = $confirmPayResponseDTO->store_transaction->terminal_id;
        $balanceFund->saveOrFail();

        return $balanceFund;
    }
}
