<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\DTOs\Sms\SendMessageDTO;
use App\Enums\ClientCodeEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsFlyClient extends BaseClient
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = config('clients.sms_fly.base_url');
        $this->token = config('clients.sms_fly.token');
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::SMS_FLY;
    }

    public function send(SendMessageDTO $messageDTO)
    {
        return $this->request($messageDTO->toArray());
    }

    protected function logRequest(HttpRequestLogDTO $request): void
    {
        Log::channel('sms-fly-client')->info($request->url, $request->toArray());
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

        return $response->json('result');

    }
}
