<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\Enums\ClientCodeEnum;
use App\Models\Company\Feature;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class BaseAtmosClient extends BaseClient
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->channel = 'atmos-client';
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::ATMOS;
    }

    public function getToken($username, $password)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])
            ->get($this->baseUrl . '/token?grant_type=client_credentials');
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/token?grant_type=client_credentials',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            params: [],
        ));

        return $response->json();
    }

    protected function activateToken(Feature $feature): void
    {
        $username = $feature->data['username'] ?? null;
        $password = $feature->data['password'] ?? null;
        $expires_in = $feature->data['expires_in'] ?? 0;
        $this->token = $feature->data['token'] ?? null;
        if ($expires_in > Carbon::now() && $this->token) {
            return;
        }
        $response = $this->getToken($username, $password);
        $this->token = $response['access_token'] ?? null;
        $expires_in = ($response['expires_in'] ?? 0) + Carbon::now();
        $feature->data['token'] = $this->token;
        $feature->data['expires_in'] = $expires_in;
        $feature->saveOrFail();
    }
}
