<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\Enums\ClientCodeEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlumClient extends BaseClient
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = config('clients.plum.base_url');
        $this->token = config('clients.plum.token');
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::PLUM;
    }

    public function scoringMib(array $data)
    {
        $response = Http::withHeaders(['Authorization' => 'Basic ' . $this->token])
            ->get($this->baseUrl . '/Scoring/GetEbDebtScore?' . http_build_query($data));
        $this->logRequest(new HttpRequestLogDTO(
            method: 'GET',
            url: $this->baseUrl . '/Scoring/GetEbDebtScore',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            params: $data,
        ));

        return $response->json('result');
    }

    public function scoringTax(array $data)
    {
        $response = Http::withHeaders(['Authorization' => 'Basic ' . $this->token])
            ->get($this->baseUrl . '/Scoring/GetTaxDebtScore?' . http_build_query($data));
        $this->logRequest(new HttpRequestLogDTO(
            method: 'GET',
            url: $this->baseUrl . '/Scoring/GetTaxDebtScore',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            params: $data,
        ));

        return $response->json('result');
    }

    protected function logRequest(HttpRequestLogDTO $request): void
    {
        Log::channel('plum-client')->info($request->url, $request->toArray());
    }
}
