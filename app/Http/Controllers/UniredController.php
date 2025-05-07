<?php

namespace App\Http\Controllers;

use App\Http\Requests\UniredCheckPaymentCallbackRequest;
use App\Http\Requests\UniredPaymentWebhookCallbackRequest;
use App\Http\Requests\UniredSendPaymentSmsCallbackRequest;
use App\Models\Company;
use App\Models\Company\Installment;
use App\Services\IcanService;
use App\Services\UniredService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

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
        $response = [
            'success' => true,
            'code' => 0,
            'message' => 'SUCCESS',
            'result' => [
                'debt' => 0,
            ],
        ];
        if ($companyId = $request->getCompanyId()) {
            tenancy()->initialize(Company::findOrFail($companyId));
            $installment = Installment::find($request->getInstallmentId());
            if ($installment) {
                $avtomatoDebt = (int) $installment->debtAmount * 100;

                if ($installment->ican_id) {
                    $debtAmount = app(IcanService::class)->getInstallmentDebtAmount($installment->ican_id);

                    if ($avtomatoDebt > $debtAmount * 100) {
                        $avtomatoDebt = $debtAmount * 100;
                    }
                }

                $response = [
                    'success' => true,
                    'code' => 0,
                    'message' => 'SUCCESS',
                    'result' => [
                        'debt' => $avtomatoDebt,
                    ],
                ];
            } else {
                $response = [
                    'success' => false,
                    'code' => 199,
                    'message' => 'Не правильные параметры: invalid_params : loan id invalid',
                    'result' => null,
                ];
            }
        }

        Log::channel('unired-callback')->info('checkPayment', [
            'request' => $request->all(),
            'response' => $response,
        ]);

        return response()->json($response, 200);
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
