<?php

namespace App\Http\Controllers;

use App\DTOs\QrPay\Payment\SbpPaymentDTO;
use App\DTOs\QrPay\Payment\SbpTransferDTO;
use App\Http\Requests\QrPay\SbpPaymentWebhookCallbackRequest;
use App\Models\Company;
use App\Models\Company\SbpTransaction;
use App\Services\CompanyQrPayService;

class QrPayCallbackController extends Controller
{
    public function __construct(
        private readonly CompanyQrPayService $qrPayService
    ) {}

    public function webHook(SbpPaymentWebhookCallbackRequest $request)
    {
        $requestDto = SbpPaymentDTO::from($request->toArray());
        if ($companyId = $requestDto->transfer->credit->getCompanyId()) {
            tenancy()->initialize(Company::findOrFail($companyId));
            $transaction = SbpTransaction::query()->where('ext_id', $requestDto->transfer->credit->ext_id)->first();
            if ($transaction !== null) {
                if ($this->qrPayService->updatePaymentByCallback($transaction, SbpTransferDTO::from($request->toArray()))) {
                    return response()->json(['success' => true, 'message' => 'OK']);
                }
            }

            return response()->json(['message' => 'Transaction was not found'], 404);
        }

        return response()->json(['message' => 'Company was not found'], 404);
    }
}
