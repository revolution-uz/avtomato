<?php

namespace App\Services;

use App\Clients\QrPayClient;
use App\DTOs\QrPay\Base\BaseQrPayResponseDTO;
use App\DTOs\QrPay\Payment\SbpCancelPaymentRequestDTO;
use App\DTOs\QrPay\Payment\SbpCreatePaymentRequestDTO;
use App\DTOs\QrPay\Payment\SbpPaymentDTO;
use App\DTOs\QrPay\Payment\SbpPaymentResponseDTO;
use App\Helpers\Utilities;
use App\Http\Requests\QrPay\QrPayCreatePaymentRequest;
use App\Models\Company\SbpTransaction;
use App\Models\SbpTerminal;

class CompanyQrPayService
{
    public function __construct(private QrPayClient $qrPayClient) {}

    public function createPayment(QrPayCreatePaymentRequest $request): SbpPaymentDTO
    {
        $terminal = SbpTerminal::query()->where('company_id', tenant('id'))->first();
        $transaction = new SbpTransaction;
        $transaction->terminal_id = $terminal->id;
        $transaction->amount = $request->amount;
        $transaction->ext_id = Utilities::generateIdByTenancy($terminal->id);
        $transaction->saveOrFail();
        $transaction->ext_id .= Utilities::TENANT_SEPARATOR . $transaction->id;
        $transaction->saveOrFail();

        return SbpPaymentDTO::from($this->qrPayClient->createPayment(new SbpCreatePaymentRequestDTO($transaction->ext_id, $transaction->amount, 'QR PAY', $transaction->terminal_id, $request->root() . '/state/' . $transaction->ext_id))->result);
    }

    public function updatePaymentByCallback(SbpPaymentResponseDTO $payment)
    {
        $transaction = SbpTransaction::query()->where('ext_id', $payment->cheque->ext_id)->first();
        $transaction->updateOrFail($payment->toArray());

        return $transaction;
    }

    public function statePayment(string $ext_id): SbpPaymentDTO
    {
        $response = SbpPaymentDTO::from($this->qrPayClient->statePayment(new SbpCancelPaymentRequestDTO($ext_id))->result);
        $this->updatePaymentByCallback($response->payment);

        return $response;
    }

    public function cancelPayment(SbpTransaction $transaction): BaseQrPayResponseDTO
    {
        return $this->qrPayClient->cancelPayment(new SbpCancelPaymentRequestDTO($transaction->ext_id));
    }
}
