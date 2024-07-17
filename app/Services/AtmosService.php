<?php

namespace App\Services;

use App\Clients\AtmosCardClient;
use App\Clients\AtmosPinflClient;
use App\DTOs\Atmos\Card\AtmosAttachCardResponseDTO;
use App\DTOs\Atmos\Card\AtmosConfirmCardResponseDTO;
use App\DTOs\Atmos\Transction\AtmosTransactionDetailDTO;
use App\Exceptions\AtmosException;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Carbon\Carbon;

class AtmosService
{
    public function __construct(private AtmosCardClient $atmosCardClient, private AtmosPinflClient $atmosPinflClient) {}

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

    public function reversePayment(Transaction $transaction): bool
    {
        $response = $this->atmosCardClient->reversePayment($transaction->external_id);
        if ($response->result->isSucces()) {
            return $transaction->delete();
        }

        return false;
    }

    public function removeCard(Card $card)
    {
        if (! $card->is_connected) {
            return [true, 'The card was not binded'];
        }
        if ($card->isBindedWithInstallment()) {
            return [false, 'The card was binded with installment'];
        }
        $response = $this->atmosCardClient->removeCard($card->external_id);
        if ($response->result->isSucces()) {
            $card->delete();
        }

        return [true, 'The card was successfully removed'];
    }

    public function withdrawAmountAndCreateCardTransaction(Card $card, int $installmentId, float &$amount): void
    {
        $uniqueId = uniqid($installmentId . '-installment-id-');
        $createPayResponseDTO = $this->atmosCardClient->createPayment($uniqueId, $amount, $uniqueId);
        if (! $createPayResponseDTO->result->isSucces()) {
            return;
        }

        $preConfirmPayResponseDTO = $this->atmosCardClient->preConfirmPayment($card->external_id, $createPayResponseDTO->transaction_id);

        if (! $preConfirmPayResponseDTO->result->isSucces()) {
            return;
        }

        $confirmPayResponseDTO = $this->atmosCardClient->confirmPayment($createPayResponseDTO->transaction_id);

        if (! $confirmPayResponseDTO->result->isSucces()) {
            return;
        }

        $transaction = new Transaction;
        $transaction->transaction_type_id = TransactionType::ATMOS_WITH_CARD;
        $transaction->external_id = (string) $confirmPayResponseDTO->store_transaction->success_trans_id;
        $transaction->amount = $confirmPayResponseDTO->store_transaction->amount / 100;
        $transaction->card_id = $card->id;
        $transaction->installment_id = $installmentId;
        $transaction->customer_id = $card->customer_id;
        $transaction->paid_at = Carbon::createFromTimestamp($confirmPayResponseDTO->store_transaction->confirm_time);

        if ($transaction->save()) {
            $amount -= $transaction->amount;
        } else {
            $this->atmosCardClient->reversePayment($createPayResponseDTO->transaction_id);
        }
    }

    public function withdrawAmountAndCreatePINFLTransaction(Customer $customer, int $installmentId, float &$amount): void
    {
        $paid_at = Carbon::now();
        $ext_id = $installmentId . '-' . $paid_at->getTimestamp();
        $createPayResponseDTO = $this->atmosPinflClient->payWithPinfl($ext_id, $customer, $installmentId, $amount);
        if (! $createPayResponseDTO->isSucces()) {
            return;
        }

        $confirmWithPinfl = $this->atmosPinflClient->payConfirmWithPinfl($ext_id, $createPayResponseDTO->transaction_id);

        if (! $confirmWithPinfl->isSucces()) {
            return;
        }
        $total_amount = 0;
        foreach ($confirmWithPinfl->details as $detail) {
            /** @var AtmosTransactionDetailDTO $detail */
            $transaction = new Transaction;
            $transaction->paid_at = $paid_at;
            $transaction->transaction_type_id = TransactionType::ATMOS_WITH_PINFL;
            $transaction->external_id = (string) $confirmWithPinfl->transaction_id;
            $transaction->amount = $detail->amount / 100;
            $transaction->installment_id = $installmentId;
            $transaction->customer_id = $customer->id;
            $transaction->save();
            $amount -= $transaction->amount;
        }
    }
}
