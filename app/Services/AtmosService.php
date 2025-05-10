<?php

namespace App\Services;

use App\Clients\AtmosCardClient;
use App\Clients\AtmosPinflClient;
use App\DTOs\Atmos\Card\AtmosAttachCardResponseDTO;
use App\DTOs\Atmos\Card\AtmosConfirmCardResponseDTO;
use App\DTOs\Atmos\Transaction\AtmosTransactionDetailDTO;
use App\DTOs\Atmos\Transaction\AtmosTransactionInfoResponseDTO;
use App\Enums\Company\AtmosPinflPaymentCode;
use App\Enums\Company\TransactionAttemptStatusEnum;
use App\Enums\Company\TransactionAttemptTypeEnum;
use App\Enums\Company\TransactionStatusEnum;
use App\Exceptions\GeneralException;
use App\Helpers\Utilities;
use App\Jobs\AtmosPinflCardUpdateJob;
use App\Models\CentralCustomer;
use App\Models\Company;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionAttempt;
use App\Models\Company\TransactionType;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
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
    public function reverseCardPayment(Transaction $transaction): bool
    {
        $response = $this->atmosCardClient->reversePayment($transaction->external_id);
        if ($response->result->isSuccess()) {
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
        /** @var TransactionAttempt $transactionAttempt */
        $transactionAttempt = TransactionAttempt::create([
            'installment_id' => $installment->id,
            'customer_id' => $customer->id,
            'status' => TransactionAttemptStatusEnum::CREATED,
            'amount' => $amount,
            'type' => TransactionAttemptTypeEnum::ATMOS_PAY_WITH_PINFL,
        ]);
        $ext_id = Utilities::generateIdByTenancy($transactionAttempt->id);

        $createPayResponseDTO = $this->atmosPinflClient->payCreate($ext_id, $customer, $installment->id, $amount);
        if (! $createPayResponseDTO->isSuccess()) {
            if ($createPayResponseDTO->isCardNotFound()) {
                AtmosPinflCardUpdateJob::dispatch($customer->id, (int) $createPayResponseDTO->code);
            }
            $transactionAttempt->failed();

            return;
        }

        $confirmWithPinfl = $this->atmosPinflClient->payConfirm($ext_id, $createPayResponseDTO->payload->transaction_id);

        if (! $confirmWithPinfl->isSuccess()) {
            $transactionAttempt->failed();

            return;
        }

        if ($confirmWithPinfl->payload->inProgress()) {
            $transactionAttempt->updateOrFail(['transaction_id' => $confirmWithPinfl->payload->transaction_id]);
            $transactionAttempt->inProgress();

            return;
        }

        if ($confirmWithPinfl->payload->isSuccess()) {
            $this->saveSuccessAttempt($customer, $installment, $transactionAttempt, $confirmWithPinfl);

            return;
        }
        $transactionAttempt->failed();
    }

    public function checkInProgress(TransactionAttempt $transactionAttempt): void
    {
        $confirmWithPinfl = $this->checkAttempt($transactionAttempt);

        if ($confirmWithPinfl->payload->isSuccess()) {
            $this->saveSuccessAttempt($transactionAttempt->customer, $transactionAttempt->installment, $transactionAttempt, $confirmWithPinfl);

            return;
        }
        if (! $confirmWithPinfl->payload->inProgress()) {
            $transactionAttempt->failed();
        }

    }

    public function checkIsSuccess(TransactionAttempt $transactionAttempt): void
    {
        $confirmWithPinfl = $this->checkAttempt($transactionAttempt);
        if ($confirmWithPinfl->isReversed()) {
            /** @var Transaction $transaction */
            $transaction = Transaction::findOrFail($transactionAttempt->transaction_id);
            $transaction->canceled();
            $transactionAttempt->failed();
            $transaction->installment->fixPaidAmount();
        }
    }

    /**
     * @throws Throwable
     * @throws GeneralException
     */
    public function reversePinflPayment(Transaction $transaction): bool
    {
        $transactionAttempts = TransactionAttempt::query()->where('transaction_id', $transaction->id)->get();
        $isReversed = false;
        foreach ($transactionAttempts as $transactionAttempt) {
            $paymentState = $this->checkAttempt($transactionAttempt);
            if ($paymentState->isSuccess() && $paymentState->payload->isSuccess()) {
                $reversePaymentState = $this->atmosPinflClient->payCancel($paymentState->payload->ext_id, $paymentState->payload->transaction_id);
                if ($reversePaymentState->isSuccess() && $reversePaymentState->payload->isCanceled()) {
                    $isReversed = true;
                }
            }
        }
        if ($isReversed) {
            $transaction->status = TransactionStatusEnum::CANCELED;
            $transaction->saveOrFail();

            return true;
        }

        return false;
    }

    /**
     * @throws GeneralException
     */
    public function updatePinflCardsList(Customer $customer, ?int $atmos_status_code = null): bool
    {
        $centralCustomer = CentralCustomer::where('pinfl', $customer->pinfl)->first();

        if ($centralCustomer) {
            if (Utilities::isNotAllowedAutoUpdateCards($centralCustomer->atmos_pinfl_searched_at)) {
                if ($atmos_status_code !== AtmosPinflPaymentCode::CARDS_NOT_FOUND->value || Utilities::isNotAllowedRefreshCardsOften($centralCustomer->atmos_pinfl_searched_at)) {
                    return true;
                }
            }
        } else {
            $centralCustomer = new CentralCustomer;
            $centralCustomer->pinfl = $customer->pinfl;
            $centralCustomer->first_name = $customer->first_name;
            $centralCustomer->last_name = $customer->last_name;
            $centralCustomer->middle_name = $customer->middle_name;
        }

        try {
            tenancy()->initialize(Company::findOrFail(Company::REVO_TENANT_ID));
            $this->atmosPinflClient = new AtmosPinflClient;

            $updateResult = $this->atmosPinflClient->updateCardsList($customer->pinfl);
            if ($updateResult->isSuccess()) {
                $centralCustomer->atmos_pinfl_searched_at = Carbon::now();

                return $centralCustomer->save();
            }
        } catch (Exception $e) {
            Log::channel('atmos-pinfl-client')->info(
                'Error updating PINFL cards list',
                [
                    'class' => get_called_class(),
                    'error' => $e->getMessage(),
                    'pinfl' => $customer->pinfl,
                ]
            );
            throw new GeneralException("Failed to update cards list for PINFL: {$customer->pinfl}");
        }

        return false; // Default fail case
    }

    private function checkAttempt(TransactionAttempt $transactionAttempt): AtmosTransactionInfoResponseDTO
    {
        $ext_id = Utilities::generateIdByTenancy($transactionAttempt->id);

        return $this->atmosPinflClient->payCheck($ext_id, $transactionAttempt->customer, $transactionAttempt->installment_id, $transactionAttempt->amount);
    }

    private function saveSuccessAttempt(Customer $customer, Installment $installment, TransactionAttempt $transactionAttempt, AtmosTransactionInfoResponseDTO $confirmWithPinfl): void
    {
        $paid_at = Carbon::now();
        $detailCount = count($confirmWithPinfl->payload->details);
        $amount = $transactionAttempt->amount;
        foreach ($confirmWithPinfl->payload->details as $detail) {
            /** @var AtmosTransactionDetailDTO $detail */
            if (! $detail->isSuccess()) {
                continue;
            }
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
