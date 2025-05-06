<?php

namespace App\Http\Controllers;

use App\Http\Requests\UniredCheckPaymentCallbackRequest;
use App\Http\Requests\UniredPaymentWebhookCallbackRequest;
use App\Http\Requests\UniredSendPaymentSmsCallbackRequest;
use App\Models\Company;
use App\Models\Company\Installment;
use App\Services\UniredService;
use Exception;
use Illuminate\Support\Carbon;

class UniredController extends Controller
{
    public function __construct(private readonly UniredService $uniredService) {}

    public function webHook(UniredPaymentWebhookCallbackRequest $request)
    {
        try {
            if ($companyId = $request->getCompanyId()) {
                tenancy()->initialize($companyId);

                $installment = Installment::query()->find($request->getInstallmentId());
                if ($installment) {
                    $customerId = $request->isGuarantorPayment()
                        ? $request->getInstallmentGuarantorCustomerId()
                        : $installment->customer_id;

                    if (Company\Transaction::query()->where('external_id', $request->ext)->exists()) {
                        $transaction = Company\Transaction::query()->where('external_id', $request->ext)->first();
                        $transaction->card_pan = $request->card['pan'] ?? '****';
                        $transaction->paid_at = Carbon::parse($request->created_at)->addHours(5);
                        $transaction->save();
                        return response()->json(['success' => true, 'message' => 'OK'], 200);
                    }

                    if ($this->uniredService->createTransaction($installment, $customerId, $request)) {
                        return response()->json(['success' => true, 'message' => 'OK'], 200);
                    }

                    return response()->json(['message' => 'Transaction has already been created'], 501);
                }

                return response()->json(['message' => 'Installment not found'], 501);
            }

            return response()->json(['message' => 'Company not found'], 501);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 501);
        }
    }

    public function checkPayment(UniredCheckPaymentCallbackRequest $request)
    {
        if ($companyId = $request->getCompanyId()) {
            tenancy()->initialize(Company::findOrFail($companyId));

            if ($debt = $this->uniredService->getInstallmentDebt($request->getInstallmentId())) {
                return response()->json([
                    'success' => true,
                    'code' => 0,
                    'message' => 'SUCCESS',
                    'result' => [
                        'debt' => $debt,
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
