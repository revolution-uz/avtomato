<?php

namespace App\Services;

use App\Clients\AtmosCardClient;
use App\DTOs\Atmos\Card\AtmosAttachCardResponseDTO;
use App\DTOs\Atmos\Card\AtmosConfirmCardResponseDTO;
use App\DTOs\Management\BalanceFundConfirmDTO;
use App\Exceptions\AtmosException;
use App\Models\Company\BalanceFund;
use Throwable;

class AtmosService
{
    public function __construct(private readonly AtmosCardClient $atmosCardClient)
    {
    }

    /**
     * @throws AtmosException
     */
    public function attachCard(string $number, string $expiry): AtmosAttachCardResponseDTO
    {
        return $this->atmosCardClient->attachCard($number, $expiry);
    }

    /**
     * @throws AtmosException
     */
    public function confirmCard(int $transaction_id, string $otp): AtmosConfirmCardResponseDTO
    {
        return $this->atmosCardClient->confirmCard($transaction_id, $otp);
    }

    /**
     * @throws AtmosException
     * @throws Throwable
     */
    public function fundBalance(BalanceFundConfirmDTO $balanceFundConfirmDTO): ?BalanceFund
    {
        $confirmCardResponseDTO = $this->atmosCardClient->confirmCard($balanceFundConfirmDTO->transaction_id, $balanceFundConfirmDTO->otp);

        if (! $confirmCardResponseDTO->result->isSucces()) {
            return null;
        }

        $createPayResponseDTO = $this->atmosCardClient->payCreate(
            'Fill balance',
            $balanceFundConfirmDTO->amount,
            'User is going to fill his own balance'
        );

        if (! $createPayResponseDTO->result->isSucces()) {
            return null;
        }

        $preConfirmPayResponseDTO = $this->atmosCardClient->payPreApply($confirmCardResponseDTO->data->card_token, $createPayResponseDTO->transaction_id);

        if ($preConfirmPayResponseDTO->result->isSucces()) {
            return null;
        }

        $confirmPayResponseDTO = $this->atmosCardClient->payApplyOfd($createPayResponseDTO->transaction_id);

        if (! $confirmPayResponseDTO->result->isSucces()) {
            return null;
        }

        $balanceFund = new BalanceFund;
        $balanceFund->amount = $balanceFundConfirmDTO->amount;
        $balanceFund->card_number = $balanceFundConfirmDTO->number;
        $balanceFund->phone_number = $balanceFundConfirmDTO->phone;
        $balanceFund->external_transaction_id = $confirmPayResponseDTO->store_transaction->success_trans_id;
        $balanceFund->terminal_id = $confirmPayResponseDTO->store_transaction->terminal_id;
        if ($balanceFund->saveOrFail()) {
            return $balanceFund;
        }

        return null;
    }

    public function setRevolutionCredentials(): void
    {
        $this->atmosCardClient->setRevolutionCredentials();
    }
}
