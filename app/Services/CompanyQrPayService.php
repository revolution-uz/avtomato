<?php

namespace App\Services;

use App\Clients\QrPayClient;
use App\DTOs\QrPay\Base\BaseQrPayResponseDTO;
use App\DTOs\QrPay\Payment\SbpCancelPaymentRequestDTO;
use App\DTOs\QrPay\Payment\SbpCreatePaymentRequestDTO;
use App\DTOs\QrPay\Payment\SbpPaymentDTO;
use App\DTOs\QrPay\Payment\SbpPaymentResponseDTO;
use App\Enums\Company\TransactionAttemptStatusEnum;
use App\Helpers\Utilities;
use App\Models\Company\SbpTransaction;
use App\Models\SbpTerminal;

class CompanyQrPayService
{
    public function __construct(private QrPayClient $qrPayClient) {}

    public function createPayment(float $amount): SbpPaymentDTO
    {
        $terminal = SbpTerminal::query()->where('company_id', tenant('id'))->first();
        $transaction = new SbpTransaction;
        $transaction->terminal_id = $terminal->id;
        $transaction->amount = $amount;
        $transaction->ext_id = Utilities::generateIdByTenancy($terminal->id);
        $transaction->status = TransactionAttemptStatusEnum::CREATED;
        $transaction->saveOrFail();
        $transaction->ext_id .= Utilities::TENANT_SEPARATOR . $transaction->id;
        $transaction->saveOrFail();

        $response = $this->qrPayClient->createPayment(
            new SbpCreatePaymentRequestDTO(
                $transaction->ext_id,
                $transaction->amount,
                'Payment for retail whose terminal name is  ' . $terminal->name,
                $transaction->terminal_id,
                tenant_route('qr-pay.check', ['ext_id' => $transaction->ext_id])
            )
        );

        return SbpPaymentDTO::from($response->result);
    }

    public function updatePaymentByCallback(SbpPaymentResponseDTO $payment)
    {
        $transaction = SbpTransaction::query()->where('ext_id', $payment->cheque->ext_id)->first();
        $transaction->updateOrFail($payment->toArray());
        if ($payment->qr->status->pay_status == 'ACWP') {
            $transaction->updateOrFail(['status' => TransactionAttemptStatusEnum::SUCCESS]);
        }
        if ($payment->qr->status->pay_status == 'NTST') {
            $transaction->updateOrFail(['status' => TransactionAttemptStatusEnum::IN_PROGRESS]);
        }
        if ($payment->qr->refund_status->pay_status == 'ACWP') {
            $transaction->updateOrFail(['status' => TransactionAttemptStatusEnum::REFUND]);
        }

        return $transaction;
    }

    public function statePayment(string $ext_id)
    {
        if (SbpTransaction::query()->where('ext_id', $ext_id)->exists()) {
            $response = SbpPaymentDTO::from($this->qrPayClient->statePayment(new SbpCancelPaymentRequestDTO($ext_id))->result);
            $this->updatePaymentByCallback($response->payment);
        }

        return null;
    }

    public function cancelPayment(SbpTransaction $transaction): BaseQrPayResponseDTO
    {
        return $this->qrPayClient->cancelPayment(new SbpCancelPaymentRequestDTO($transaction->ext_id));
    }
}
