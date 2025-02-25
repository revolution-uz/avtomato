<?php

namespace App\Services;

use App\Clients\UniredSbpQRPayClient;
use App\DTOs\QrPay\Payment\SbpCreateTransferRequestDTO;
use App\DTOs\QrPay\Payment\SbpPaymentDTO;
use App\DTOs\QrPay\Payment\SbpPaymentResponseDTO;
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
                'https://api.avtomato.uz/qr-pay/callback/' . $transaction->ext_id,
                $terminal->purpose_code
            )
        );
        if (! $response->isSuccess()) {
            $transaction->updateOrFail(['status' => TransactionAttemptStatusEnum::FAILED]);
            throw new UniredException('Failed to create cheque: ' . $response->error->message);
        }

        return $this->updatePaymentByCallback($transaction, SbpPaymentDTO::from($response->result)->transfer);
    }

    public function updatePaymentByCallback(SbpTransaction $transaction, SbpPaymentResponseDTO $payment)
    {
        $transaction->credit = $payment->credit->toArray();
        $transaction->debit = $payment->debit->toArray();
        $transaction->currency = $payment->currency->rate;
        $transaction->sender = $payment->sender->toArray();
        $transaction->receiver = $payment->receiver->toArray();

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
            $response = SbpPaymentDTO::from($this->qrPayClient->stateTransfer($ext_id)->result);
            $this->updatePaymentByCallback($transaction, $response->transfer);
        }

        return null;
    }
}
