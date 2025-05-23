<?php

namespace App\Clients;

use App\DTOs\QrPay\Base\BaseQrPayResponseDTO;
use App\DTOs\QrPay\Payment\SbpCreateTransferRequestDTO;
use App\DTOs\QrPay\Terminal\SbpCreateAccountRequestDTO;
use App\Exceptions\UniredException;
use App\Helpers\HideSensitiveDataFromLogging;
use GuzzleHttp\Utils;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class UniredCardClient
{
    const METHOD_TERMINAL_LIST = 'terminal.list';

    protected const METHOD_LOGIN = 'login';

    private const METHOD_TERMINAL_CREATE = 'terminal.create';
    private const METHOD_TERMINAL_DEACTIVATE = 'terminal.deactivate';
    private const METHOD_TRANSFER_CREATE = 'transfer.create.qr.acc';
    private const METHOD_TRANSFER_STATE = 'transfer.state';

    protected string $baseUrl;

    protected string $login;

    protected string $password;
    protected string $secret;

    private string $channel;

    public function __construct()
    {
        $this->baseUrl = config('clients.unired.card.base_url');
        $this->channel = 'unired-card-client';
        $this->login = config('clients.unired.card.login');
        $this->password = config('clients.unired.card.password');
        $this->secret = config('clients.unired.card.secret');
    }

    /**
     * @throws UniredException
     */
    public function createAccount(SbpCreateAccountRequestDTO $params): BaseQrPayResponseDTO
    {
        return $this->makeRequest(self::METHOD_TERMINAL_CREATE, $params->toArray());
    }

    /**
     * @throws UniredException
     */
    public function deactivateAccount(int $account_id): BaseQrPayResponseDTO
    {
        return $this->makeRequest(self::METHOD_TERMINAL_DEACTIVATE, ['account_id' => $account_id]);
    }

    /**
     * @throws UniredException
     */
    public function createTransfer(SbpCreateTransferRequestDTO $params): BaseQrPayResponseDTO
    {
        return $this->makeRequest(self::METHOD_TRANSFER_CREATE, $params->toArray());
    }

    /**
     * @throws UniredException
     */
    public function stateTransfer(string $ext_id): BaseQrPayResponseDTO
    {
        return $this->makeRequest(self::METHOD_TRANSFER_STATE, ['ext_id' => $ext_id]);
    }

    /**
     * @throws UniredException
     */
    public function makeRequest(string $method, array $originalParams, bool $isTriedReAuthorize = false): BaseQrPayResponseDTO|string
    {
        $params = [
            'id' => uniqid(),
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $originalParams,
        ];
        try {
            $headers = $this->getAuthorizationHeaders($params);

            $response = Http::withHeaders($headers)->acceptJson()->post($this->baseUrl, $params);
            $jsonResponse = $response->json();
            Log::channel($this->channel)->info($method, [
                'client' => get_called_class(),
                'method' => $method,
                'params' => HideSensitiveDataFromLogging::handle($params),
                'response' => $jsonResponse,
            ]);
        } catch (Throwable $th) {
            Log::channel($this->channel)->error('request_exception', [
                'client' => get_called_class(),
                'response' => $response ?? null,
                'exception' => $th->getMessage(),
            ]);
            if (str_starts_with($th->getMessage(), 'cURL error')) {
                throw new UniredException(trans('api.request_create_error'));
            }
            throw new UniredException($th->getMessage());
        }

        if (is_array($jsonResponse) && isset($jsonResponse['error'])) {
            $errorCode = $jsonResponse['error']['code'] ?? 0;

            if (! $isTriedReAuthorize && ($errorCode === -32001 || $errorCode === -32106 || $errorCode === -36042)) { // this means unauthorized
                $this->getAccessToken(false);

                return $this->makeRequest($method, $originalParams, true);
            }

            $this->throwUniredExceptionFromArray($jsonResponse['error']);
        }

        if (isset($jsonResponse['result']['error'])) {
            throw new UniredException($this->getErrorMessageFromUniredProperty($jsonResponse['result']['error']));
        }

        if (! isset($jsonResponse['result'])) {
            throw new UniredException(json_encode($jsonResponse));
        }

        return BaseQrPayResponseDTO::from($jsonResponse);
    }

    public function getAuthorizationHeaders(array $params): array
    {
        return [
            'Header-Sign' => $this->generateSignature($params),
            'Header-Login' => $this->login,
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ];
    }

    public function getAccessToken(bool $fromCache = true): ?string
    {
        $accessToken = null;

        if ($fromCache) {
            $accessToken = Cache::get($this->baseUrl);
        }

        if (! $accessToken) {
            $params = [
                'jsonrpc' => '2.0',
                'id' => uniqid(),
                'method' => self::METHOD_LOGIN,
                'params' => [
                    'username' => $this->login,
                    'password' => $this->password,
                ],
            ];

            $header = [
                'Content-Type' => 'application/json',
                'Header-Login' => $this->login,
                'Header-Sign' => $this->generateSignature($params),
            ];

            $response = Http::withHeaders($header)->acceptJson()->post($this->baseUrl, $params)->json();
            Log::channel($this->channel)->info('getAccessToken', [
                'header' => $header,
                'client' => get_called_class(),
                'method' => self::METHOD_LOGIN,
                'params' => HideSensitiveDataFromLogging::handle($params),
                'response' => $response,
            ]);
            if ($accessToken = Arr::get($response, 'result.access_token')) {
                Cache::put($this->baseUrl, $accessToken);
            }
        }

        return $accessToken;
    }

    public function generateSignature(array $data): string
    {
        $digest = hash_hmac('sha256', Utils::jsonEncode($data), $this->secret, true);

        return base64_encode($digest);
    }

    public function syncAccounts(): BaseQrPayResponseDTO
    {
        return $this->makeRequest(self::METHOD_TERMINAL_LIST, []);
    }

    /**
     * @throws UniredException
     */
    private function throwUniredExceptionFromArray(mixed $uniredException)
    {
        $errorCode = $uniredException['code'] ?? 0;
        if (isset($uniredException['message'])) {
            if (isset($uniredException['message']['message'])) {
                throw new UniredException(
                    $this->getErrorMessageFromUniredProperty($uniredException['message']['message'])
                );
            }

            throw new UniredException(
                $this->getErrorMessageFromUniredProperty($uniredException['message'])
            );
        }

        throw new UniredException(
            $this->getErrorMessageFromUniredProperty($uniredException)
        );
    }

    private function getErrorMessageFromUniredProperty($uniredProperty)
    {
        if (is_string($uniredProperty)) {
            return $uniredProperty;
        }

        if (isset($uniredProperty[app()->getLocale()])) {
            return $uniredProperty[app()->getLocale()];
        }

        if (app()->getLocale() === 'oz') {
            return $uniredProperty['uz'] ?? $uniredProperty['ru'] ?? json_encode($uniredProperty);
        }

        if (app()->getLocale() === 'tj') {
            return $uniredProperty['ru'] ?? json_encode($uniredProperty);
        }

        return json_encode($uniredProperty);
    }
}
