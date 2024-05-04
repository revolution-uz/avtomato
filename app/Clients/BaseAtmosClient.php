<?php

namespace App\Clients;

use App\DTOs\Atmos\AtmosAuthTokenDTO;
use App\DTOs\Atmos\AtmosCredentialsDTO;
use App\DTOs\HttpRequestLogDTO;
use App\Enums\FeatureEnum;
use App\Exceptions\AtmosException;
use App\Models\Company\Feature;
use Illuminate\Support\Facades\Http;
use Throwable;

abstract class BaseAtmosClient extends BaseClient
{
    const GET_TOKEN_URI = '/token';

    protected ?FeatureEnum $featureName = null;
    protected bool $isFeature = true;
    protected ?string $baseUrl;

    protected ?string $username = null;
    protected ?string $password = null;
    protected ?string $token = null;
    protected ?int $store_id = null;

    protected function getOrRefreshToken(string $username, string $password, string $token = null): AtmosAuthTokenDTO
    {
        $data = [
            'grant_type' => 'client_credentials',
        ];
        if ($token) {
            $data['refresh_token'] = $token;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->post($this->baseUrl . self::GET_TOKEN_URI, $data);

        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . self::GET_TOKEN_URI,
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            params: [],
        ));

        return AtmosAuthTokenDTO::from($response->json());
    }

    protected function activateRevolutionToken()
    {
        $response = $this->getOrRefreshToken($this->username, $this->password);
        $this->token = $response->token_type . ' ' . $response->access_token;

    }

    /**
     * @throws Throwable
     */
    protected function activateFeatureToken(): void
    {
        $feature = Feature::findByName($this->featureName?->value);

        if (! $feature->is_active) {
            return;
        }
        $credentials = new AtmosCredentialsDTO(
            $feature->data['username'] ?? null,
            $feature->data['password'] ?? null,
            $feature->data['store_id'] ?? null,
            $feature->data['expires_in'] ?? null,
            $feature->data['access_token'] ?? null,
            $feature->data['token_type'] ?? null
        );

        if (! $credentials->username) {
            return;
        }

        if ($credentials->expires_in <= time()) {
            $response = $this->getOrRefreshToken($credentials->username, $credentials->password, $credentials->access_token);
            $credentials->access_token = $response->access_token;
            $credentials->expires_in = $response->expires_in + time();
            $credentials->token_type = $response->token_type;

            $feature->data = $credentials->toArray();
            $feature->saveOrFail();
        }

        $this->store_id = $credentials->store_id;
        $this->token = $credentials->token_type . ' ' . $credentials->access_token;
    }

    /**
     * @throws AtmosException|Throwable
     */
    protected function makeRequest(string $path, array $data, string $method = 'post'): array
    {
        $this->activateToken();
        $response = Http::withHeaders(['Authorization' => $this->token])
            ->$method($this->baseUrl . $path, $data);

        if (
            $response->status() !== 200 &&
            $response->status() !== 201
        ) {
            parent::logRequest(
                new HttpRequestLogDTO(
                    method: $method,
                    url: $this->baseUrl . $path,
                    status: $response->status(),
                    response: $response->body(),
                    duration: $response->transferStats?->getTransferTime(),
                    body: $data,
                ),
                'error'
            );

            throw new AtmosException($response['description'], $response['code']);
        }

        parent::logRequest(new HttpRequestLogDTO(
            method: $method,
            url: $this->baseUrl . $path,
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json();
    }

    private function activateToken()
    {
        if ($this->isFeature) {
            $this->activateFeatureToken();
        } else {
            $this->activateRevolutionToken();
        }
    }
}
