<?php

namespace App\Services\User;

use App\Clients\AtmosBalanceClient;
use App\DTOs\Atmos\AtmosResultDTO;
use App\DTOs\Atmos\Card\AtmosAttachCardResponseDTO;
use App\DTOs\Atmos\Card\AtmosCardDTO;
use App\DTOs\Management\BalanceFundConfirmDTO;
use App\Enums\Company\CompanyStatusEnum;
use App\Enums\FeatureEnum;
use App\Exceptions\AtmosException;
use App\Models\Company\BalanceCharge;
use App\Models\Company\BalanceFund;
use App\Models\Company\Feature;
use App\Services\ConfirmationService;
use Illuminate\Database\Eloquent\Model;

readonly class BalanceService
{
    public function __construct(
        private AtmosBalanceClient $atmosBalanceClient,
        private ConfirmationService $confirmationService
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

    public function isBalanceEnoughForFeature(FeatureEnum $featureName): bool
    {
        /** @var Feature $feature */
        $feature = Feature::query()->where('name', $featureName)->firstOrFail();

        return $this->getBalance() >= $feature->chargeable_amount;
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
    public function requestFillBalance(string $number, string $expiry): AtmosAttachCardResponseDTO
    {
        $response = $this->atmosBalanceClient->attachCard($number, $expiry);

        if ($response->data instanceof AtmosCardDTO) {
            $this->confirmationService->send($response->data->phone);

            return AtmosAttachCardResponseDTO::from(['result' => AtmosResultDTO::from(['code' => 'OK']), 'phone' => $response->data->phone, 'transaction_id' => $response->data->card_id]);
        }

        return $response;
    }

    public function fundBalance(BalanceFundConfirmDTO $balanceFundConfirmDTO): BalanceFund
    {
        $isConfirmed = $balanceFundConfirmDTO->phone && $this->confirmationService->confirm($balanceFundConfirmDTO->phone, $balanceFundConfirmDTO->otp);

        if ($isConfirmed) {
            $confirmCardResponseDTO = $this->atmosBalanceClient->attachCard($balanceFundConfirmDTO->number, $balanceFundConfirmDTO->expiry);
            if (! $confirmCardResponseDTO->data or ! $confirmCardResponseDTO->data instanceof AtmosCardDTO) {
                throw new AtmosException($confirmCardResponseDTO->result->description);
            }
        } else {
            $confirmCardResponseDTO = $this->atmosBalanceClient->confirmCard($balanceFundConfirmDTO->transaction_id, $balanceFundConfirmDTO->otp);

            if (! $confirmCardResponseDTO->result->isSucces()) {
                throw new AtmosException($confirmCardResponseDTO->result->description);
            }
        }

        $createPayResponseDTO = $this->atmosBalanceClient->createPayment(
            'Fill balance',
            $balanceFundConfirmDTO->amount,
            'User is going to fill his own balance'
        );

        if (! $createPayResponseDTO->result->isSucces()) {
            throw new AtmosException($confirmCardResponseDTO->result->description);
        }

        $preConfirmPayResponseDTO = $this->atmosBalanceClient->preConfirmPayment($confirmCardResponseDTO->data->card_token, $createPayResponseDTO->transaction_id);

        if (! $preConfirmPayResponseDTO->result->isSucces()) {
            throw new AtmosException($confirmCardResponseDTO->result->description);
        }

        $confirmPayResponseDTO = $this->atmosBalanceClient->confirmPayment($createPayResponseDTO->transaction_id);

        if (! $confirmPayResponseDTO->result->isSucces()) {
            throw new AtmosException($confirmCardResponseDTO->result->description);
        }

        $balanceFund = new BalanceFund;
        $balanceFund->amount = $balanceFundConfirmDTO->amount;
        $balanceFund->card_number = $balanceFundConfirmDTO->number;
        $balanceFund->phone_number = $balanceFundConfirmDTO->phone;
        $balanceFund->external_transaction_id = $confirmPayResponseDTO->store_transaction->success_trans_id;
        $balanceFund->terminal_id = $confirmPayResponseDTO->store_transaction->terminal_id;
        $balanceFund->saveOrFail();

        return $balanceFund;
    }
}
