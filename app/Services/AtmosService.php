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
use App\Models\Company\Installment;
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

    public function withdrawAmountAndCreateCardTransaction(Installment $installment, Card $card, $amount): ?Transaction
    {
        $uniqueId = uniqid($installment->id . '-installment-id-');
        $createPayResponseDTO = $this->atmosCardClient->createPayment($uniqueId, $amount, $uniqueId);
        if (! $createPayResponseDTO->result->isSucces()) {
            return null;
        }

        $preConfirmPayResponseDTO = $this->atmosCardClient->preConfirmPayment($card->external_id, $createPayResponseDTO->transaction_id);

        if (! $preConfirmPayResponseDTO->result->isSucces()) {
            return null;
        }

        $confirmPayResponseDTO = $this->atmosCardClient->confirmPayment($createPayResponseDTO->transaction_id);

        if (! $confirmPayResponseDTO->result->isSucces()) {
            return null;
        }

        $transaction = new Transaction;
        $transaction->transaction_type_id = TransactionType::ATMOS_WITH_CARD;
        $transaction->external_id = (string) $confirmPayResponseDTO->store_transaction->success_trans_id;
        $transaction->amount = $confirmPayResponseDTO->store_transaction->total / 100;
        $transaction->card_id = $card->id;
        $transaction->installment_id = $installment->id;
        $transaction->customer_id = $card->customer_id;
        $transaction->paid_at = Carbon::createFromTimestamp($confirmPayResponseDTO->store_transaction->confirm_time);
        $transaction->save();

        return $transaction;
    }

    public function withdrawAmountAndCreatePINFLTransaction(Installment $installment, Customer $customer, $amount): float|int|null
    {
        $paid_at = Carbon::now();
        $ext_id = $installment->id . '-' . $paid_at->getTimestamp();
        $createPayResponseDTO = $this->atmosPinflClient->payWithPinfl($ext_id, $customer, $installment->id, $amount);
        if (! $createPayResponseDTO->isSucces()) {
            return null;
        }

        $confirmWithPinfl = $this->atmosPinflClient->payConfirmWithPinfl($ext_id, $createPayResponseDTO->transaction_id);

        if (! $confirmWithPinfl->isSucces()) {
            return null;
        }
        $total_amount = 0;
        foreach ($confirmWithPinfl->details as $detail) {
            /** @var AtmosTransactionDetailDTO $detail */
            $transaction = new Transaction;
            $transaction->paid_at = $paid_at;
            $transaction->transaction_type_id = TransactionType::ATMOS_WITH_PINFL;
            $transaction->external_id = (string) $confirmWithPinfl->transaction_id;
            $transaction->amount = $detail->amount / 100;
            $transaction->installment_id = $installment->id;
            $transaction->customer_id = $customer->id;
            $transaction->save();
            $total_amount += $transaction->amount;
        }

        return $total_amount;
    }
}
