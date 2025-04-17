<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\DTOs\Ican\IcanAuthTokenDTO;
use App\DTOs\Ican\IcanCredentialsDTO;
use App\DTOs\Ican\IcanResultDTO;
use App\Enums\FeatureEnum;
use App\Exceptions\GeneralException;
use App\Models\Company\Feature;
use Illuminate\Support\Facades\Http;
use Throwable;

abstract class BaseIcanClient extends BaseClient
{
    const GET_TOKEN_URI = 'https://api.base.icangroup.uz/common/auth/login';

    protected ?FeatureEnum $featureName = null;
    protected ?string $baseUrl;

    protected function getNewToken(string $username, string $password): IcanAuthTokenDTO
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Connection' => 'close',
        ])
            ->timeout(150)
            ->connectTimeout(150)
            ->post(self::GET_TOKEN_URI, ['username' => $username, 'password' => $password]);

        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: self::GET_TOKEN_URI,
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            params: [],
        ));

        return IcanAuthTokenDTO::from($response->json());
    }

    /**
     * @throws GeneralException
     */
    protected function makeRequest(string $path, array $data, string $method = 'post', $is_responsible = true): array
    {
        if (! $token = $this->getActiveToken()) {
            return ['result' => new IcanResultDTO('ERROR', ['password' => 'Cannot activate the token'])];
        }

        $url = $this->baseUrl . $path . '?' . http_build_query($data);

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
                'Content-Type' => 'application/json',
                'Connection' => 'close',
            ])->timeout(150)
                ->connectTimeout(150)
                ->$method($url);

            parent::logRequest(new HttpRequestLogDTO(
                method: $method,
                url: $this->baseUrl . $path,
                status: $response->status(),
                response: $response->body(),
                duration: $response->transferStats?->getTransferTime(),
                body: $data,
            ));

            if (! in_array($response->status(), [200, 201]) || $is_responsible && empty($response->json())) {
                throw new GeneralException($response->json('description', 'Unexpected response'), $response->json('code', 0));
            }

            return $is_responsible ? $response->json() : ['code' => $response->status()];
        } catch (Throwable $exception) {
            parent::logRequest(new HttpRequestLogDTO(
                method: $method,
                url: $url,
                status: $exception->getCode(),
                response: $exception->getMessage(),
                duration: null,
                headers: ['Authorization' => $token],
                body: $data,
            ), 'error');
            throw new GeneralException($exception->getMessage(), $exception->getCode());
        }
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

        $credentials = IcanCredentialsDTO::from($feature->data);

        if (! $credentials->username) {
            return null;
        }

        if ($credentials->expires_in <= time()) {
            $response = $this->getNewToken($credentials->username, $credentials->password);
            $credentials->access_token = $response->token;
            $credentials->expires_in = now()->addHour()->timestamp;
            $feature->data = $credentials->toArray();

            $feature->saveOrFail();
        }

        return $credentials->access_token;
    }
}
