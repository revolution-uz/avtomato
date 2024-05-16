<?php

namespace App\Clients;

use App\DTOs\Atmos\AtmosAuthTokenDTO;
use App\DTOs\Atmos\AtmosCredentialsDTO;
use App\DTOs\Atmos\AtmosResultDTO;
use App\DTOs\HttpRequestLogDTO;
use App\Enums\FeatureEnum;
use App\Exceptions\AtmosException;
use App\Models\Company\Feature;
use Illuminate\Support\Facades\Http;
use Throwable;

abstract class BaseAtmosClient extends BaseClient
{
    const GET_TOKEN_URI = '/token?grant_type=client_credentials';

    protected ?FeatureEnum $featureName = null;
    protected bool $isFeature = true;
    protected ?string $baseUrl;

    protected ?string $username = null;
    protected ?string $password = null;
    protected ?string $token = null;
    protected ?int $store_id = null;

    protected function getOrRefreshToken(string $username, string $password): AtmosAuthTokenDTO
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->post($this->baseUrl . self::GET_TOKEN_URI);

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

    protected function activateRevolutionToken(): bool
    {
        $response = $this->getOrRefreshToken($this->username, $this->password);
        $this->token = $response->token_type . ' ' . $response->access_token;

        return true;

    }

    /**
     * @throws Throwable
     */
    protected function activateFeatureToken(): bool
    {
        $feature = Feature::findByName($this->featureName?->value);

        if (! $feature->is_active || ! $feature->data) {
            return false;
        }

        $credentials = AtmosCredentialsDTO::from($feature->data);
        if (! $credentials->username) {
            return false;
        }

        $this->store_id = $credentials->store_id;

        if ($credentials->expires_in <= time()) {
            $response = $this->getOrRefreshToken($credentials->username, $credentials->password);
            $credentials->access_token = $response->access_token;
            $credentials->expires_in = $response->expires_in + time();
            $credentials->token_type = $response->token_type;
            $feature->data = $credentials->toArray();

            $feature->saveOrFail();
        }

        $this->token = $credentials->token_type . ' ' . $credentials->access_token;

        return true;
    }

    /**
     * @throws AtmosException|Throwable
     */
    protected function makeRequest(string $path, array $data, string $method = 'post'): array
    {
        if (! $this->activateToken()) {
            return ['result' => new AtmosResultDTO('ERROR', 'Cannot activate the token')];
        }
        $data['lang'] = app()->getLocale();
        $response = Http::withHeaders([
            'Authorization' => $this->token,
            'Content-Type' => 'application/json',
            'Connection' => 'close',
        ])
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
                    headers: ['Authorization' => $this->token],
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
            return $this->activateFeatureToken();
        } else {
            return $this->activateRevolutionToken();
        }
    }
}
