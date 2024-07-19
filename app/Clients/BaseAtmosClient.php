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

abstract class BaseAtmosClient extends BaseClient
{
    const GET_TOKEN_URI = '/token?grant_type=client_credentials';

    protected ?FeatureEnum $featureName = null;
    protected ?string $baseUrl;

    protected function getNewToken(string $username, string $password): AtmosAuthTokenDTO
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->timeout(150)->connectTimeout(150)->post($this->baseUrl . self::GET_TOKEN_URI);

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

    /**
     * @throws AtmosException
     */
    protected function makeRequest(string $path, array $data, string $method = 'post'): array
    {
        if (! $token = $this->getActiveToken()) {
            return ['result' => new AtmosResultDTO('ERROR', 'Cannot activate the token')];
        }
        $data['lang'] = app()->getLocale();
        $response = Http::withHeaders([
            'Authorization' => $token,
            'Content-Type' => 'application/json',
            'Connection' => 'close',
        ])->timeout(150)->connectTimeout(150)
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
                    headers: ['Authorization' => $token],
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

    protected function getActiveToken(): ?string
    {
        if (! tenancy()->initialized) {
            return null;
        }

        $feature = Feature::findByName($this->featureName);

        if (! $feature->is_active || ! $feature->data) {
            return null;
        }

        $credentials = AtmosCredentialsDTO::from($feature->data);
        if (! $credentials->username) {
            return null;
        }

        if ($credentials->expires_in <= time()) {
            $response = $this->getNewToken($credentials->username, $credentials->password);
            $credentials->access_token = $response->access_token;
            $credentials->expires_in = $response->expires_in + time();
            $credentials->token_type = $response->token_type;
            $feature->data = $credentials->toArray();

            $feature->saveOrFail();
        }

        return $credentials->token_type . ' ' . $credentials->access_token;
    }

    protected function getStoreId(): ?int
    {
        if (! tenancy()->initialized) {
            return null;
        }

        $feature = Feature::findByName($this->featureName);

        if (! $feature->is_active || ! $feature->data) {
            return null;
        }

        return AtmosCredentialsDTO::from($feature->data)->store_id;
    }
}
