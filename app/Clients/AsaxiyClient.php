<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\Enums\ClientCodeEnum;
use Illuminate\Support\Facades\Http;

class AsaxiyClient extends BaseClient
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = config('clients.asaxiy.base_url');
        $this->token = config('clients.asaxiy.token');
        $this->channel = 'asaxiy-client';
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::ASAXIY;
    }

    public function createRequest(array $data): ?int
    {
        $response = Http::withHeaders(['x-access-token' => $this->token])
            ->post($this->baseUrl . '/katm/create-request', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/katm/create-request',
            status: $response->status(),
            response: $response->json(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('data.request_id');
    }

    public function getRequestStatus(int $extId): ?array
    {
        $params = ['request_id' => $extId];
        $response = Http::withHeaders(['x-access-token' => $this->token])
            ->get($this->baseUrl . '/katm/get-request', $params);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'GET',
            url: $this->baseUrl . '/katm/get-request',
            status: $response->status(),
            response: $response->json(),
            duration: $response->transferStats?->getTransferTime(),
            params: $params,
        ));

        return $response->json('data');
    }

    public function scoringMib(array $params)
    {
        $response = Http::withHeaders(['x-access-token' => $this->token])
            ->get($this->baseUrl . '/katm/mib?' . http_build_query($params));
        $this->logRequest(new HttpRequestLogDTO(
            method: 'GET',
            url: $this->baseUrl . '/katm/mib',
            status: $response->status(),
            response: $response->json(),
            duration: $response->transferStats?->getTransferTime(),
            params: $params,
        ));

        return $response->json('result');
    }

    public function scoringTax(array $params)
    {
        $response = Http::withHeaders(['x-access-token' => $this->token])
            ->get($this->baseUrl . '/katm/tax?' . http_build_query($params));
        $this->logRequest(new HttpRequestLogDTO(
            method: 'GET',
            url: $this->baseUrl . '/katm/tax',
            status: $response->status(),
            response: $response->json(),
            duration: $response->transferStats?->getTransferTime(),
            params: $params,
        ));

        return $response->json('result');
    }

    public function scoringHumo(array $data)
    {
        $response = Http::withHeaders(['x-access-token' => $this->token])
            ->post($this->baseUrl . '/katm/humo', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/katm/humo',
            status: $response->status(),
            response: $response->json(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('result');
    }

    public function scoringUzcard(array $data)
    {
        $response = Http::withHeaders(['x-access-token' => $this->token])
            ->post($this->baseUrl . '/katm/uzcard', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/katm/uzcard',
            status: $response->status(),
            response: $response->json(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('result');
    }
}
