<?php

namespace App\Services;

use App\Clients\AtmosCardClient;
use App\DTOs\Atmos\Card\AtmosAttachCardResponseDTO;
use App\DTOs\Atmos\Card\AtmosConfirmCardResponseDTO;
use App\DTOs\Management\BalanceFundConfirmDTO;
use App\Exceptions\AtmosException;
use App\Http\Resources\Customer\CardRemoveResource;
use App\Models\Company\BalanceFund;
use App\Models\Company\Card;
use App\Models\Company\Transaction;
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

        $createPayResponseDTO = $this->atmosCardClient->createPayment(
            'Fill balance',
            $balanceFundConfirmDTO->amount,
            'User is going to fill his own balance'
        );

        if (! $createPayResponseDTO->result->isSucces()) {
            return null;
        }

        $preConfirmPayResponseDTO = $this->atmosCardClient->preConfirmPayment($confirmCardResponseDTO->data->card_token, $createPayResponseDTO->transaction_id);

        if (! $preConfirmPayResponseDTO->result->isSucces()) {
            return null;
        }

        $confirmPayResponseDTO = $this->atmosCardClient->confirmPayment($createPayResponseDTO->transaction_id);

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

        $this->atmosCardClient->removeCard($confirmCardResponseDTO->data->card_token);

        return null;
    }

    public function setRevolutionCredentials(): void
    {
        $this->atmosCardClient->setRevolutionCredentials();
    }

    public function reversePayment(Transaction $transaction): void
    {
        $response = $this->atmosCardClient->reversePayment($transaction->external_id);
        if ($response->result->isSucces()) {
            $transaction->delete();
        }
    }

    public function removeCard(Card $card): CardRemoveResource
    {
        if ($card->isBindedWithInstallment()) {
            return new CardRemoveResource(false, 'The card was binded with installment');
        }
        $response = $this->atmosCardClient->removeCard($card->external_id);
        if ($response->result->isSucces()) {
            $card->delete();
        }

        return new CardRemoveResource(true, 'The card was successfully removed');
    }
}
