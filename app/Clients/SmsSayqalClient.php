<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\DTOs\Sms\SmsSayqal\SmsSayqalGetStatusResponseDTO;
use App\DTOs\Sms\SmsSayqal\SmsSayqalSendMessageResponseDTO;
use App\Enums\ClientCodeEnum;
use App\Exceptions\GeneralException;
use App\Helpers\Utilities;
use App\Models\Company\SmsMessage;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Throwable;

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

    public function sendMessage(int $id, string $phone, string $text): SmsSayqalSendMessageResponseDTO
    {
        return SmsSayqalSendMessageResponseDTO::from($this->request([
            'username' => $this->username,
            'service' => [
                'service' => 4,
            ],
            'message' => [
                'smsid' => Utilities::generateIdByTenancy($id),
                'phone' => $this->format($phone),
                'text' => $text,
            ],
        ], '/sms/TransmitSMS'));
    }

    public function getStatus(SmsMessage $message): SmsSayqalGetStatusResponseDTO
    {
        return SmsSayqalGetStatusResponseDTO::from([
            'transactionid' => $message->ext_id,
            'smsid' => Utilities::generateIdByTenancy($message->id),
        ], '/sms/StatusSMS');
    }

    /**
     * @throws Throwable
     * @throws ConnectionException
     */
    private function request(array $data, string $method)
    {
        try {
            $unixTime = time();
            $response = Http::withHeaders(['Content-Type' => 'application/json', 'X-Access-Token' => $this->generateTransmitAccessToken($unixTime)])
                ->post($this->baseUrl . $method, array_merge($data, ['utime' => $unixTime]));
            if ($response->status() == 200) {
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
        } catch (Throwable $exception) {
            $this->logRequest(new HttpRequestLogDTO(
                method: 'POST',
                url: $this->baseUrl . $method,
                status: $exception->getCode(),
                response: $exception->getMessage(),
                duration: $exception->getTraceAsString(),
                body: $data,
            ), 'error');
            throw new GeneralException($exception->getMessage());
        }

    }

    private function generateTransmitAccessToken(int $unixTime): string
    {
        $Access = "TransmitSMS {$this->username} {$this->secret} {$unixTime}";

        return md5($Access);
    }
}
