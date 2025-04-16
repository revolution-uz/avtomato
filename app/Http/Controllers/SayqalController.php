<?php

namespace App\Http\Controllers;

use App\Http\Requests\SayqalSmsCallbackRequest;
use App\Http\Requests\SayqalVoiceCallbackRequest;
use App\Models\Company;
use App\Services\SmsService;
use Exception;

class SayqalController extends Controller
{
    public function __construct(private readonly SmsService $smsService) {}

    public function sms(SayqalSmsCallbackRequest $request)
    {
        try {
            tenancy()->initialize($request->getCompanyId());
            $this->smsService->callbackSayqalSms($request);

            return response()->json(['message' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 501);
        }
    }

    public function voice(SayqalVoiceCallbackRequest $request)
    {
        if ($companyId = $request->getCompanyId()) {
            tenancy()->initialize(Company::findOrFail($companyId));

            if ($this->smsService->callbackSayqalVoice($request)) {
                return response()->json(['success'], 200);
            }
        }

        return response()->json(['message' => 'Message was not found'], 501);
    }
}
