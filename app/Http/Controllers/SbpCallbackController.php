<?php

namespace App\Http\Controllers;

use App\Http\Requests\SbpPaymentWebhookCallbackRequest;
use App\Models\Company;
use App\Models\Company\SbpTerminal;
use App\Services\QrPayService;

class SbpCallbackController extends Controller
{
    public function __construct(
        private readonly QrPayService $qrPayService
    ) {}

    public function webHook(SbpPaymentWebhookCallbackRequest $request)
    {
        if ($companyId = $request->getCompanyId()) {
            tenancy()->initialize(Company::findOrFail($companyId));
            $terminal = SbpTerminal::findOrFail($request->getTerminalId());
            if ($terminal !== null) {
                if ($request->isGuarantorPayment()) {
                    if ($this->qrPayService->transferRegistry($request->ext_id)) {
                        return response()->json(['success' => true, 'message' => 'OK'], 200);
                    }
                } elseif ($this->qrPayService->transferRegistry($request->ext_id)) {
                    return response()->json(['success' => true, 'message' => 'OK'], 200);
                }
            }
        }

        return response()->json(['message' => 'Terminal was not found'], 501);
    }
}
