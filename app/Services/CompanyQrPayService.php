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

class CompanyQrPayService
{
    public function __construct(private UniredSbpQRPayClient $qrPayClient) {}

    public function createPayment(float $amount): SbpPaymentDTO
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
                'https://api.avtomato.uz/qr-pay/callback/' . $transaction->ext_id
            )
        );
        if (! $response->isSuccess()) {
            $transaction->updateOrFail(['status' => TransactionAttemptStatusEnum::FAILED]);
            throw new UniredException('Failed to create cheque: ' . $response->error->message);
        }
        $result = SbpPaymentDTO::from($response->result);
        $transaction->credit = $result->transfer->credit->toArray();
        $transaction->debit = $result->transfer->debit->toArray();
        $transaction->currency = $result->transfer->currency->rate;
        $transaction->saveOrFail();

        return $result;
    }

    public function updatePaymentByCallback(SbpPaymentResponseDTO $payment)
    {
        $transaction = SbpTransaction::query()->where('ext_id', $payment->credit->ext_id)->first();
        $transaction->updateOrFail($payment->toArray());
        if ($payment->credit->isSuccess() && $payment->debit->isSuccess()) {
            $transaction->updateOrFail(['status' => TransactionAttemptStatusEnum::SUCCESS]);
        }
        if ($payment->credit->isSuccess()) {
            $transaction->updateOrFail(['status' => TransactionAttemptStatusEnum::IN_PROGRESS]);
        }

        return $transaction;
    }

    public function statePayment(string $ext_id)
    {
        if (SbpTransaction::query()->where('ext_id', $ext_id)->exists()) {
            $response = SbpPaymentDTO::from($this->qrPayClient->stateTransfer($ext_id)->result);
            $this->updatePaymentByCallback($response->transfer);
        }

        return null;
    }
}
