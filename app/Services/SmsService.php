<?php

namespace App\Services;

use App\Clients\SmsFlyClient;
use App\Clients\SmsSayqalClient;
use App\DTOs\FeatureData\FeatureSmsData;
use App\DTOs\Sms\SmsSayqal\SmsSayqalGetStatusResponseDTO;
use App\DTOs\Sms\SmsSayqal\SmsSayqalSendMessageResponseDTO;
use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\SmsMessageStatusEnum;
use App\Enums\Company\SmsTemplateEnum;
use App\Enums\FeatureEnum;
use App\Enums\SmsProviderEnum;
use App\Exceptions\CompanyIsNotActiveException;
use App\Http\Requests\SayqalSmsCallbackRequest;
use App\Http\Requests\SayqalVoiceCallbackRequest;
use App\Models\BaseFeature;
use App\Models\Company\CardConfirmationSms;
use App\Models\Company\Feature;
use App\Models\Company\SmsMessage;
use App\Models\Company\SmsTemplate;
use Exception;

class SmsService
{
    /**
     * @throws Exception
     */
    public function sendSms(Feature $feature, SmsMessage $message): SmsMessage
    {
        if ($feature->name !== FeatureEnum::SMS) {
            throw new Exception('Feature not allowed');
        } elseif (tenant('status') != CompanyStatusEnum::ACTIVE) {
            throw new CompanyIsNotActiveException;
        }

        $data = FeatureSmsData::from($feature->data ?? $feature->original_data);

        $client = match (SmsProviderEnum::from($data->provider)) {
            SmsProviderEnum::SMS_SAYQAL => new SmsSayqalClient($data->username, $data->secret),
            SmsProviderEnum::SMS_FLY => new SmsFlyClient($data->secret),
        };

        $response = $client->sendMessage(id: $message->id, phone: $message->phone, text: $message->message);

        if ($response instanceof SmsSayqalSendMessageResponseDTO) {
            $message->update([
                'ext_id' => $response->transactionid,
                'status' => SmsMessageStatusEnum::ACCEPTED,
            ]);
        }

        return $message;
    }

    /**
     * @throws Exception
     */
    public function refreshStatus(Feature $feature, SmsMessage $message): SmsMessage
    {
        if ($feature->name !== FeatureEnum::SMS) {
            throw new Exception('Feature not allowed');
        }

        $data = FeatureSmsData::from($feature->data ?? $feature->original_data);

        $client = match (SmsProviderEnum::from($data->provider)) {
            SmsProviderEnum::SMS_SAYQAL => new SmsSayqalClient($data->username, $data->secret),
            SmsProviderEnum::SMS_FLY => new SmsFlyClient($data->secret),
        };

        $response = $client->getStatus($message);

        if ($response instanceof SmsSayqalGetStatusResponseDTO) {
            $message->update([
                'status' => $response->status,
            ]);
        }

        return $message;
    }

    public function sendConfirmationSms(CardConfirmationSms $confirmationSms, SmsTemplateEnum $smsTemplateEnum): void
    {
        $feature = BaseFeature::where('name', FeatureEnum::SMS)->firstOrFail();
        $template = SmsTemplate::query()->findOrFail($smsTemplateEnum->value);

        $data = FeatureSmsData::from($feature->original_data);

        $client = match (SmsProviderEnum::from($data->provider)) {
            SmsProviderEnum::SMS_SAYQAL => new SmsSayqalClient($data->username, $data->secret),
            SmsProviderEnum::SMS_FLY => new SmsFlyClient($data->secret),
        };

        $client->sendMessage(
            id: $confirmationSms->id,
            phone: $confirmationSms->phone,
            text: $template->getMessage(['code' => $confirmationSms->code, 'signature' => request()->header('signature', '')])
        );
    }

    public function callbackSayqalSms(SayqalSmsCallbackRequest $request): bool
    {
        $message = SmsMessage::query()->find($request->getSmsId());

        if (! $message) {
            return false;
        }

        $message->update([
            'status' => $request->status,
            'updated_at' => $request->statusdate,
        ]);

        if ($request->status == SmsMessageStatusEnum::DELIVERED->value) {
            $feature = Feature::findByName(FeatureEnum::SMS);
            $message->charge->update(['amount' => $feature->chargeable_amount * $request->smsCount()]);
        } else {
            $message->charge->delete();
        }

        return true;
    }

    public function callbackSayqalVoice(SayqalVoiceCallbackRequest $request)
    {
        return $request->getRequestId();
    }
}
