<?php

namespace App\Http\Controllers;

use App\Http\Requests\SayqalSmsCallbackRequest;
use App\Http\Requests\SayqalVoiceCallbackRequest;
use App\Models\Company;
use App\Models\Company\Installment;
use App\Services\SmsService;

class SayqalController extends Controller
{
    public function __construct(
        private readonly SmsService $smsService
    ) {}

    public function sms(SayqalSmsCallbackRequest $request)
    {
        if ($company = Company::findOrFail($request->getCompanyId())) {
            tenancy()->initialize($company);

            return $this->smsService->callbackSayqalSms($request);
        }
    }

    public function voice(SayqalVoiceCallbackRequest $request)
    {
        if ($company = Company::findOrFail($request->getCompanyId())) {
            tenancy()->initialize($company);

            return $this->smsService->callbackSayqalVoice($request);
        }

        return $this->smsService->generateInstallmentFormStream(Installment::find($request->installment_id));
    }
}
