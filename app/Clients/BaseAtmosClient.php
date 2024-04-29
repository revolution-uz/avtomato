<?php

namespace App\Clients;

use App\DTOs\Atmos\AtmosCredentialsDTO;
use App\DTOs\HttpRequestLogDTO;
use App\Models\Company\Feature;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

abstract class BaseAtmosClient extends BaseClient
{
    const GET_TOKEN_URI = '/token?grant_type=client_credentials';

    protected ?string $baseUrl;
    protected ?string $token;
    protected ?int $store_id;

    public function getToken($username, $password)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->get($this->baseUrl . self::GET_TOKEN_URI);

        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . self::GET_TOKEN_URI,
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            params: [],
        ));

        return $response->json();
    }

    protected function activateToken(Feature $feature): void
    {
        if (! $feature->is_active) {
            return;
        }
        $credentials = new AtmosCredentialsDTO($feature->data['username'] ?? null, $feature->data['password'] ?? null, $feature->data['store_id'] ?? null, $feature->data['expires_in'] ?? null, $feature->data['access_token'] ?? null);
        if ($credentials->expires_in > time()) {
            return;
        }
        $response = $this->getToken($credentials->username, $credentials->password);
        $credentials->access_token = $response['access_token'] ?? null;
        $credentials->expires_in = $response['expires_in'] ?? 0;
        $this->store_id = $credentials->store_id;
        $this->token = $credentials->access_token;
        $credentials->expires_in += time();
        $feature->data = $credentials->toArray();
        $feature->saveOrFail();
    }

    protected function makeRequest($path, $data, $method = 'post'): Response
    {
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->$method($this->baseUrl . $path, $data);

        parent::logRequest(new HttpRequestLogDTO(
            method: $method,
            url: $this->baseUrl . $path,
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response;
    }
}
