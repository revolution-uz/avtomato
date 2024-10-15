<?php

namespace App\Services;

use App\Clients\AtmosCardClient;
use App\Clients\AtmosPinflClient;
use App\DTOs\Atmos\Card\AtmosAttachCardResponseDTO;
use App\DTOs\Atmos\Card\AtmosConfirmCardResponseDTO;
use App\DTOs\Atmos\Transaction\AtmosTransactionDetailDTO;
use App\Enums\Company\TransactionAttemptStatusEnum;
use App\Enums\Company\TransactionAttemptTypeEnum;
use App\Enums\Company\TransactionStatusEnum;
use App\Exceptions\GeneralException;
use App\Helpers\Utilities;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionAttempt;
use App\Models\Company\TransactionType;
use Carbon\Carbon;
use Throwable;

class AtmosService
{
    public function __construct(private AtmosCardClient $atmosCardClient, private AtmosPinflClient $atmosPinflClient) {}

    /**
     * @throws GeneralException
     */
    public function attachCard(string $number, string $expiry): AtmosAttachCardResponseDTO
    {
        return $this->atmosCardClient->attachCard($number, $expiry);
    }

    /**
     * @throws GeneralException
     */
    public function confirmCard(int $transaction_id, string $otp): AtmosConfirmCardResponseDTO
    {
        return $this->atmosCardClient->confirmCard($transaction_id, $otp);
    }

    /**
     * @throws Throwable
     * @throws GeneralException
     */
    public function reversePayment(Transaction $transaction): bool
    {
        $response = $this->atmosCardClient->reversePayment($transaction->external_id);
        if ($response->result->isSuccess()) {
            $installment = $transaction->installment;
            $installment->paid_amount -= $transaction->amount;
            $installment->saveOrFail();
            $transaction->status = TransactionStatusEnum::CANCELED;
            $transaction->saveOrFail();

            return true;
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
        if ($response->result->isSuccess()) {
            $card->delete();
        }

        return [true, 'The card was successfully removed'];
    }

    public function withdrawAmountAndCreateCardTransaction(Card $card, Installment $installment, float &$amount): void
    {
        /** @var TransactionAttempt $transactionAttempt */
        $transactionAttempt = TransactionAttempt::create([
            'installment_id' => $installment->id,
            'customer_id' => $card->customer_id,
            'status' => TransactionAttemptStatusEnum::CREATED,
            'amount' => $amount,
            'card_id' => $card->id,
            'type' => TransactionAttemptTypeEnum::ATMOS_PAY_WITH_CARD,
        ]);

        $createPayResponseDTO = $this->atmosCardClient->createPayment($transactionAttempt->id, $amount, $transactionAttempt->id);
        if (! $createPayResponseDTO->result->isSuccess()) {
            $transactionAttempt->failed();

            return;
        }

        $preConfirmPayResponseDTO = $this->atmosCardClient->preConfirmPayment($card->external_id, $createPayResponseDTO->transaction_id);

        if (! $preConfirmPayResponseDTO->result->isSuccess()) {
            $transactionAttempt->failed();

            return;
        }

        $confirmPayResponseDTO = $this->atmosCardClient->confirmPayment($createPayResponseDTO->transaction_id);

        if (! $confirmPayResponseDTO->result->isSuccess()) {
            $transactionAttempt->failed();

            return;
        }

        $transaction = new Transaction;
        $transaction->transaction_type_id = TransactionType::ATMOS_WITH_CARD;
        $transaction->external_id = (string) $confirmPayResponseDTO->store_transaction->success_trans_id;
        $transaction->amount = $confirmPayResponseDTO->store_transaction->amount / 100;
        $transaction->card_id = $card->id;
        $transaction->installment_id = $installment->id;
        $transaction->customer_id = $card->customer_id;
        $transaction->card_pan = $card->pan;
        $transaction->branch_id = $installment->branch_id;
        $transaction->paid_at = Carbon::createFromTimestampMs($confirmPayResponseDTO->store_transaction->confirm_time);

        if ($transaction->save()) {
            $amount -= $transaction->amount;
            $transactionAttempt->success($transaction->id);
        } else {
            $this->atmosCardClient->reversePayment($createPayResponseDTO->transaction_id);
            $transactionAttempt->failed();
        }
    }

    public function withdrawAmountAndCreatePINFLTransaction(Customer $customer, Installment $installment, float &$amount): void
    {
        $paid_at = Carbon::now();
        /** @var TransactionAttempt $transactionAttempt */
        $transactionAttempt = TransactionAttempt::create([
            'installment_id' => $installment->id,
            'customer_id' => $customer->id,
            'status' => TransactionAttemptStatusEnum::CREATED,
            'amount' => $amount,
            'type' => TransactionAttemptTypeEnum::ATMOS_PAY_WITH_PINFL,
        ]);
        $ext_id = Utilities::generateIdByTenancy($transactionAttempt->id);

        $createPayResponseDTO = $this->atmosPinflClient->payWithPinfl($ext_id, $customer, $installment->id, $amount);
        if (! $createPayResponseDTO->isSuccess()) {
            $transactionAttempt->failed();

            return;
        }

        $confirmWithPinfl = $this->atmosPinflClient->payConfirmWithPinfl($ext_id, $createPayResponseDTO->payload->transaction_id);

        if (! $confirmWithPinfl->isSuccess()) {
            $transactionAttempt->failed();

            return;
        }
        $detailCount = count($confirmWithPinfl->payload->details);
        foreach ($confirmWithPinfl->payload->details as $detail) {
            /** @var AtmosTransactionDetailDTO $detail */
            $transaction = new Transaction;
            $transaction->paid_at = $paid_at;
            $transaction->transaction_type_id = TransactionType::ATMOS_WITH_PINFL;
            $transaction->external_id = $confirmWithPinfl->payload->transaction_id;
            $transaction->card_pan = $detail->pan;
            $transaction->amount = $detail->amount / 100;
            $transaction->installment_id = $installment->id;
            $transaction->branch_id = $installment->branch_id;
            $transaction->customer_id = $customer->id;
            $transaction->save();
            $amount -= $transaction->amount;
            $transactionAttempt->success($transaction->id);
            $detailCount--;
            if ($detailCount) {
                $transactionAttempt = TransactionAttempt::create([
                    'installment_id' => $installment->id,
                    'customer_id' => $customer->id,
                    'status' => TransactionAttemptStatusEnum::CREATED,
                    'amount' => $amount,
                    'type' => TransactionAttemptTypeEnum::ATMOS_PAY_WITH_PINFL,
                ]);
            }
        }
    }
}
