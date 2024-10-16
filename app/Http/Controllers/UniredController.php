<?php

namespace App\Http\Controllers;

use App\Http\Requests\UniredCheckPaymentCallbackRequest;
use App\Http\Requests\UniredPaymentWebhookCallbackRequest;
use App\Http\Requests\UniredSendPaymentSmsCallbackRequest;
use App\Models\Company;
use App\Models\Company\Customer;
use App\Services\UniredService;

class UniredController extends Controller
{
    public function __construct(
        private readonly UniredService $uniredService
    ) {}

    public function webHook(UniredPaymentWebhookCallbackRequest $request)
    {
        if ($companyId = $request->getCompanyId()) {
            tenancy()->initialize(Company::findOrFail($companyId));

            if ($this->uniredService->createTransaction(Customer::findOrFail($request->getClientId()), $request)) {
                return response()->json(['success' => true, 'message' => 'OK'], 200);
            }
        }

        return response()->json(['message' => 'Client was not found'], 501);
    }

    public function checkPayment(UniredCheckPaymentCallbackRequest $request)
    {
        if ($companyId = $request->getCompanyId()) {
            tenancy()->initialize(Company::findOrFail($companyId));

            if ($debt = $this->uniredService->getClientDebt($request->getClientId())) {
                return response()->json([
                    'success' => true,
                    'code' => 0,
                    'message' => 'SUCCESS',
                    'result' => [
                        'debt' => $debt * 100,
                    ],
                ], 200);
            }
            if ($debt === null) {
                return response()->json([
                    'success' => false,
                    'code' => 199,
                    'message' => 'Не правильные параметры: invalid_params : loan id invalid',
                    'result' => null,
                ], 200);
            }
        }

        return response()->json([
            'success' => true,
            'code' => 0,
            'message' => 'SUCCESS',
            'result' => [
                'debt' => 0,
            ],
        ], 200);
    }

    public function sendSms(UniredSendPaymentSmsCallbackRequest $request)
    {
        if ($companyId = $request->getCompanyId()) {
            tenancy()->initialize(Company::findOrFail($companyId));

            if ($this->uniredService->sendPaymentSms($request->getPhone(), $request->getCardPan())) {
                return response()->json(['success' => true, 'message' => 'OK'], 200);
            }
        }

        return response()->json(['message' => 'Client was not found'], 501);
    }
}
