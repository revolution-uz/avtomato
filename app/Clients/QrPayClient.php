<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\DTOs\QrPay\Base\BaseQrPayDTO;
use App\DTOs\QrPay\Base\BaseQrPayRequestDTO;
use App\DTOs\QrPay\Base\BaseQrPayResponseDTO;
use App\DTOs\QrPay\Payment\SbpCreateTransferRequestDTO;
use App\DTOs\QrPay\Payment\SbpPaymentRequestDTO;
use App\DTOs\QrPay\Terminal\SbpCreateAccountRequestDTO;
use App\Enums\ClientCodeEnum;
use App\Exceptions\QrPayException;
use App\Exceptions\UniredException;
use App\Helpers\HideSensitiveDataFromLogging;
use GuzzleHttp\Utils;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class QrPayClient extends BaseClient
{
    // Authorization Module
    const LOGIN = 'login';

    const ACCOUNT_CREATE = 'account.create';
    const QR_TRANSFER_CREATE = 'qr.transfer.create';
    const QR_TRANSFER_STATE = 'qr.transfer.state';

    protected string $baseUrl;

    protected string $login;

    protected string $password;

    protected string $secret;

    public function __construct()
    {
        $this->baseUrl = config('clients.qr_pay.base_url');
        $this->channel = 'qr-pay-client';
        $this->login = config('clients.qr_pay.login');
        $this->password = config('clients.qr_pay.password');
        $this->secret = config('clients.qr_pay.secret');
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::QR_PAY;
    }

    public function createAccount(SbpCreateAccountRequestDTO $params): BaseQrPayResponseDTO
    {
        return $this->makeRequest(self::ACCOUNT_CREATE, $params);
    }

    public function createTransfer(SbpCreateTransferRequestDTO $params): BaseQrPayResponseDTO
    {
        return $this->makeRequest(self::QR_TRANSFER_CREATE, $params);
    }

    public function stateTransfer(SbpPaymentRequestDTO $params): BaseQrPayResponseDTO
    {
        return $this->makeRequest(self::QR_TRANSFER_STATE, $params);
    }

    private function getAccessToken(bool $fromCache = true): ?string
    {
        $accessToken = null;

        if ($fromCache) {
            $accessToken = Cache::get($this->baseUrl);
        }

        if (! $accessToken) {
            $params = $this->loginParams();

            $header = $this->headerAttributes();

            $response = Http::withHeaders($header)->acceptJson()->post($this->baseUrl, $params)->json();
            Log::channel($this->channel)->info('response', [
                'header' => $header,
                'client' => get_called_class(),
                'method' => self::LOGIN,
                'params' => HideSensitiveDataFromLogging::handle($params),
                'response' => $response,
            ]);
            if ($accessToken = Arr::get($response, 'result.access_token')) {
                Cache::put($this->baseUrl, $accessToken);
            }
        }

        return $accessToken;
    }

    private function refreshAccessToken(bool $fromCache = true): ?string
    {
        $accessToken = null;

        if ($fromCache) {
            $accessToken = Cache::get($this->baseUrl);
        }

        if (! $accessToken || ! is_string($accessToken) || empty(trim($accessToken))) {

            $response = Http::acceptJson()->post($this->baseUrl, $this->loginParams())->json();

            if ($accessToken = Arr::get($response, 'result.access_token')) {
                Cache::put($this->baseUrl, $accessToken);
            }
        }

        return $accessToken;
    }

    private function getAuthorizationHeaders(array $params): array
    {
        return [
            'Header-Sign' => $this->generateSignature($params),
            'Header-Login' => $this->login,
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ];
    }

    private function generateSignature(array $data): string
    {
        $digest = hash_hmac('sha256', Utils::jsonEncode($data), $this->secret, true);

        return base64_encode($digest);
    }

    private function httpRequest(BaseQrPayRequestDTO $data, string $httpMethod, bool $isTriedReAuthorize = false): BaseQrPayResponseDTO
    {
        try {
            $response = Http::withHeaders($this->getAuthorizationHeaders($data->toArray()))
                ->$httpMethod($this->baseUrl, $data->toArray());
            $jsonResponse = $response->json();

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
                $this->refreshAccessToken(false);

                return $this->httpRequest($data, $httpMethod, true);
            }

            $this->throwUniredExceptionFromArray($jsonResponse['error']);
        }

        if (isset($jsonResponse['result']['error'])) {
            throw new UniredException($this->getErrorMessageFromUniredProperty($jsonResponse['result']['error']));
        }

        if (! isset($jsonResponse['result'])) {
            throw new UniredException(json_encode($jsonResponse));
        }

        $this->logRequest(new HttpRequestLogDTO(
            method: $httpMethod,
            url: $this->baseUrl,
            status: $response->status(),
            response: $response->json(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data->toArray(),
        ));

        return BaseQrPayResponseDTO::from($response->json());
    }

    private function makeRequest(string $method, BaseQrPayDTO $params, string $httpMethod = 'post'): BaseQrPayResponseDTO
    {
        $response = $this->httpRequest(new BaseQrPayRequestDTO($method, $params, '2.0', time()), $httpMethod);

        if (! $response->isSuccess()) {
            throw new QrPayException($response->error->message);
        }

        return $response;
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

    private function loginParams()
    {
        return [
            'jsonrpc' => '2.0',
            'id' => uniqid(),
            'method' => self::LOGIN,
            'params' => [
                'username' => $this->login,
                'password' => $this->password,
            ],
        ];
    }

    private function headerAttributes()
    {
        return [
            'Content-Type' => 'application/json',
            'Header-Login' => $this->login,
            'Header-Sign' => $this->generateSignature($this->loginParams()),
        ];
    }
}
