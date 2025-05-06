<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\DTOs\Ican\IcanAuthTokenDTO;
use App\DTOs\Ican\IcanCredentialsDTO;
use App\Enums\FeatureEnum;
use App\Exceptions\GeneralException;
use App\Models\Company\Feature;
use Illuminate\Support\Facades\Http;
use Throwable;

abstract class BaseIcanClient extends BaseClient
{
    const LOGIN = '/common/auth/login';

    protected ?FeatureEnum $featureName;
    protected ?string $baseUrl;

    private ?string $authBaseUrl;

    public function __construct()
    {
        $this->authBaseUrl = config('clients.ican.auth_base_url');
        $this->baseUrl = config('clients.ican.base_url');
        $this->channel = 'ican-client';
        $this->featureName = FeatureEnum::ICAN;
    }

    protected function getNewToken(string $username, string $password): IcanAuthTokenDTO
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Connection' => 'close',
        ])
            ->timeout(150)
            ->connectTimeout(150)
            ->post($this->authBaseUrl . self::LOGIN, ['username' => $username, 'password' => $password]);

        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->authBaseUrl . self::LOGIN,
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
    protected function makeRequest(string $path, array $data, string $method = 'post'): array
    {
        return $this->sendHttpRequest($this->baseUrl . $path, $data, $method);
    }

    /**
     * @throws GeneralException
     */
    protected function makeAuthRequest(string $path, array $data, string $method = 'post'): array
    {
        return $this->sendHttpRequest($this->authBaseUrl . $path, $data, $method);
    }

    /**
     * @throws GeneralException
     */
    private function sendHttpRequest(string $url, array $data, string $method, bool $try = true): array
    {
        if (! $token = $this->getActiveToken()) {
            throw new GeneralException('Cannot activate the token');
        }

        $url .= count($data) > 0 ? '?' . http_build_query($data) : '';

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
                url: $url,
                status: $response->status(),
                response: $response->body(),
                duration: $response->transferStats?->getTransferTime(),
                body: $data,
            ));

            if (! $response->successful() || empty($response->json())) {
                if ($response->status() === 429) {
                    parent::logRequest(new HttpRequestLogDTO(
                        method: $method,
                        url: $url,
                        status: $response->status(),
                        response: $response->body(),
                        duration: $response->transferStats?->getTransferTime(),
                        body: $data,
                    ));
                    $retryAfter = $response->json('seconds_left', 60);
                    sleep($retryAfter);

                    return $this->makeRequest($url, $data, $method);
                }

                throw new GeneralException($response->json('message', 'Unexpected response'));
            }

            return $response->json();

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

            if ($try) {
                sleep(5);
                return $this->sendHttpRequest($url, $data, $method, false);
            }

            throw new GeneralException($exception->getMessage());
        }
    }

    private function getActiveToken(): ?string
    {
        if (! tenancy()->initialized) {
            return null;
        }

        $feature = Feature::findByName($this->featureName);

        if (! $feature->is_active || ! $feature->data) {
            return null;
        }

        $credentials = IcanCredentialsDTO::from($feature->data);

        if (! $credentials->username || ! $credentials->password) {
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
