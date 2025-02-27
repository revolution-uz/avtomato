<?php

namespace App\Http\Controllers;

use App\DTOs\QrPay\Payment\SbpPaymentDTO;
use App\DTOs\QrPay\Payment\SbpTransferDTO;
use App\Helpers\HideSensitiveDataFromLogging;
use App\Http\Requests\QrPay\SbpPaymentWebhookCallbackRequest;
use App\Models\Company;
use App\Models\Company\SbpTransaction;
use App\Services\CompanyQrPayService;
use Illuminate\Support\Facades\Log;

class QrPayCallbackController extends Controller
{
    public function __construct(
        private readonly CompanyQrPayService $qrPayService
    ) {}

    public function webHook(SbpPaymentWebhookCallbackRequest $request)
    {
        $requestDto = SbpPaymentDTO::from($request->toArray());
        $response = response()->json(['message' => 'Company was not found'], 404);
        if ($companyId = $requestDto->transfer->credit->getCompanyId()) {
            tenancy()->initialize(Company::findOrFail($companyId));
            $transaction = SbpTransaction::query()->where('ext_id', $requestDto->transfer->credit->ext_id)->first();
            if ($transaction !== null) {
                if ($this->qrPayService->updatePaymentByCallback($transaction, SbpTransferDTO::from($request->toArray()))) {
                    $response = response()->json(['success' => true, 'message' => 'OK']);
                }
            } else {
                $response = response()->json(['message' => 'Transaction was not found'], 404);
            }
        }
        $this->logging($request, $response);

        return $response;
    }

    private function logging($request, $response)
    {
        Log::channel('qr-pay-client')->info('callback', [
            'header' => HideSensitiveDataFromLogging::handle($request->headers->all()),
            'client' => get_called_class(),
            'method' => 'webHook',
            'params' => HideSensitiveDataFromLogging::handle($request),
            'response' => $response,
        ]);
    }
}
