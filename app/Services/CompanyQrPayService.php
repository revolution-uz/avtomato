<?php

namespace App\Services;

use App\Clients\UniredSbpQRPayClient;
use App\DTOs\QrPay\Payment\SbpCreateTransferRequestDTO;
use App\DTOs\QrPay\Payment\SbpPaymentResponseDTO;
use App\DTOs\QrPay\Payment\SbpTransferDTO;
use App\Enums\Company\TransactionAttemptStatusEnum;
use App\Exceptions\UniredException;
use App\Helpers\Utilities;
use App\Models\Company\SbpTransaction;
use App\Models\SbpAccount;
use Carbon\Carbon;

class CompanyQrPayService
{
    public function __construct(private UniredSbpQRPayClient $qrPayClient) {}

    public function createPayment(float $amount): SbpPaymentResponseDTO
    {
        $terminal = SbpAccount::query()->where('company_id', tenant('id'))->first();
        $transaction = new SbpTransaction;
        $transaction->terminal_id = $terminal->id;
        $transaction->amount = $amount;
        $transaction->ext_id = Utilities::generateIdByTenancy($terminal->id);
        $transaction->status = TransactionAttemptStatusEnum::CREATED;
        $transaction->saveOrFail();
        $transaction->ext_id .= Utilities::TENANT_SEPARATOR . $transaction->id;
        $transaction->saveOrFail();

        $response = $this->qrPayClient->createTransfer(
            new SbpCreateTransferRequestDTO(
                $transaction->ext_id,
                $transaction->amount * 100,
                $terminal->terminal_id,
                route('qrPayCallback.webHook', ['extId' => $transaction->ext_id]),
                $terminal->purpose_code
            )
        );
        if (! $response->isSuccess()) {
            $transaction->updateOrFail(['status' => TransactionAttemptStatusEnum::FAILED]);
            throw new UniredException('Failed to create cheque: ' . $response->error->message);
        }

        return $this->updatePaymentByCallback($transaction, SbpTransferDTO::from($response->result));
    }

    public function updatePaymentByCallback(SbpTransaction $transaction, SbpTransferDTO $payment)
    {
        $transaction->credit = $payment->transfer->credit;
        $transaction->debit = $payment->transfer->debit;
        $transaction->sender = $payment->transfer->sender;
        $transaction->receiver = $payment->transfer->receiver;
        $transaction->currency = $payment->transfer->currency->rate;
        $payment = SbpPaymentResponseDTO::from($payment->transfer->toArray());

        if ($payment->credit->isSuccess() && $payment->debit->isSuccess()) {
            $transaction->status = TransactionAttemptStatusEnum::SUCCESS;
            $transaction->payment_updated_at = Carbon::now();
        } elseif ($payment->credit->isSuccess()) {
            $transaction->status = TransactionAttemptStatusEnum::IN_PROGRESS;
        } elseif ($payment->credit->isCancelled() || $payment->debit->isCancelled()) {
            $transaction->status = TransactionAttemptStatusEnum::REFUND;
        } elseif ($payment->credit->isFailed()) {
            $transaction->status = TransactionAttemptStatusEnum::FAILED;
        }

        $transaction->saveOrFail();

        return $payment;
    }

    public function statePayment(string $ext_id)
    {
        $transaction = SbpTransaction::query()->where('ext_id', $ext_id)->first();
        if ($transaction != null) {
            $response = $this->qrPayClient->stateTransfer($ext_id);
            $this->updatePaymentByCallback($transaction, SbpTransferDTO::from($response->result));
        }

        return null;
    }
}
