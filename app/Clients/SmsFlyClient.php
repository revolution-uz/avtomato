<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\DTOs\Sms\SendMessageDTO;
use App\DTOs\Sms\SmsFlyResponseDTO;
use App\Enums\ClientCodeEnum;
use Illuminate\Support\Facades\Http;

class SmsFlyClient extends BaseClient
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = config('clients.sms_fly.base_url');
        $this->token = config('clients.sms_fly.token');
        $this->channel = 'sms-fly-client';
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::SMS_FLY;
    }

    public function send(SendMessageDTO $messageDTO): SmsFlyResponseDTO
    {
        return SmsFlyResponseDTO::from($this->request($messageDTO->toArray()));
    }

    private function request(array $data, $method = '/send')
    {

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post($this->baseUrl . $method, array_merge($data, ['key' => $this->token]));

        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . $method,
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));
        $responseBody = json_decode($response->body(), true);
        if (isset($responseBody['success'])) {
            return $responseBody;
        }

        return [
            'success' => false,
            'reason' => $responseBody['message'] ?? "Avtomato didn't get error message",
            'resultCode' => $responseBody['status'] ?? 500,
        ];

    }
}
