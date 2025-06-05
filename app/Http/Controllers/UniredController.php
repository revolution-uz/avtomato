<?php

namespace App\Http\Controllers;

use App\Http\Requests\UniredCheckPaymentCallbackRequest;
use App\Http\Requests\UniredPaymentWebhookCallbackRequest;
use App\Http\Requests\UniredSendPaymentSmsCallbackRequest;
use App\Models\Company;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
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

                    Transaction::query()->updateOrCreate([
                        'external_id' => $request->ext,
                    ], [
                        'transaction_type_id' => TransactionType::UNIRED_WITH_PINFL,
                        'branch_id' => $installment->branch_id,
                        'external_id' => $request->ext,
                        'amount' => $request->amount / 100,
                        'installment_id' => $installment->id,
                        'customer_id' => $customerId,
                        'card_pan' => $request->card['pan'] ?? '****',
                        'card_owner' => $request->card['owner'] ?? null,
                        'card_phone' => empty($request->card['phone']) ? null : $request->card['phone'],
                        'paid_at' => Carbon::parse($request->created_at)->addHours(5),
                    ]);

                    return response()->json(['success' => true, 'message' => 'OK'], 200);
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
            'code' => 101,
            'debt' => 0,
        ];
        if ($companyId = $request->getCompanyId()) {
            tenancy()->initialize(Company::findOrFail($companyId));
            $installment = Installment::find($request->getInstallmentId());
            if ($installment) {
                $avtomatoDebt = (int) $installment->debtAmount * 100;

                if ($installment->ican_id) {
                    $debtAmount = app(IcanService::class)->getInstallmentDebtAmount($installment->ican_id) * 100;

                    if ($avtomatoDebt > $debtAmount) {
                        $avtomatoDebt = $debtAmount;
                    }
                }

                $response = [
                    'code' => 100,
                    'debt' => max(0, $avtomatoDebt),
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
