<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\DTOs\Sms\SmsSayqal\SmsSayqalGetStatusResponseDTO;
use App\DTOs\Sms\SmsSayqal\SmsSayqalSendMessageResponseDTO;
use App\Enums\ClientCodeEnum;
use App\Models\Company\SmsMessage;
use Illuminate\Support\Facades\Http;

class SmsSayqalClient extends BaseSmsClient
{
    private string $baseUrl;

    public function __construct(private readonly string $username, private readonly string $secret)
    {
        $this->baseUrl = config('clients.sms_sayqal.base_url');
        $this->channel = 'sms-sayqal-client';
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::SMS_SAYQAL;
    }

    public function sendMessage(int $id, int $phone, string $text): SmsSayqalSendMessageResponseDTO
    {
        return SmsSayqalSendMessageResponseDTO::from($this->request([
            'username' => $this->username,
            'service' => [
                'service' => 4,
            ],
            'message' => [
                'smsid' => $id,
                'phone' => $phone,
                'text' => $text,
            ],
        ], '/sms/TransmitSMS'));
    }

    public function getStatus(SmsMessage $message): SmsSayqalGetStatusResponseDTO
    {
        return SmsSayqalGetStatusResponseDTO::from([
            'transactionid' => $message->ext_id,
            'smsid' => $message->id,
        ], '/sms/StatusSMS');
    }

    private function request(array $data, string $method)
    {
        $unixTime = time();
        $response = Http::withHeaders(['Content-Type' => 'application/json', 'X-Access-Token' => $this->generateTransmitAccessToken($unixTime)])
            ->post($this->baseUrl . $method, array_merge($data, ['utime' => $unixTime]));

        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . $method,
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json();
    }

    private function generateTransmitAccessToken(int $unixTime): string
    {
        $Access = "TransmitSMS {$this->username} {$this->secret} {$unixTime}";

        return md5($Access);
    }
}
