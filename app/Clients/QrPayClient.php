<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\DTOs\QrPay\Base\BaseQrPayDTO;
use App\DTOs\QrPay\Base\BaseQrPayRequestDTO;
use App\DTOs\QrPay\Base\BaseQrPayResponseDTO;
use App\DTOs\QrPay\Payment\SbpCancelPaymentRequestDTO;
use App\DTOs\QrPay\Payment\SbpCreatePaymentRequestDTO;
use App\DTOs\QrPay\QrPayErrorResponseDTO;
use App\DTOs\QrPay\System\QrPayCredentialsDTO;
use App\DTOs\QrPay\System\QrPayLoginRequestDTO;
use App\DTOs\QrPay\System\QrPayLoginResponseDTO;
use App\DTOs\QrPay\Terminal\SbpAddTerminalRequestDTO;
use App\DTOs\QrPay\Terminal\SbpDeactivateTerminalRequestDTO;
use App\DTOs\QrPay\Terminal\SbpUpdateTerminalRequestDTO;
use App\Enums\ClientCodeEnum;
use App\Enums\FeatureEnum;
use App\Exceptions\QrPayException;
use App\Models\Company\Feature;
use Illuminate\Support\Facades\Http;

class QrPayClient extends BaseClient
{
    // Authorization Module
    const LOGIN = 'login';

    // Terminal Module
    const TERMINAL_ADD = 'terminal.add';
    const TERMINAL_UPDATE = 'terminal.update';
    const TERMINAL_DEACTIVATE = 'terminal.deactivate';

    // Payment Module
    const PAYMENT_CREATE = 'payment.create';
    const PAYMENT_STATE = 'payment.state';
    const PAYMENT_CANCEL = 'payment.cancel';

    private ?string $baseUrl;
    private FeatureEnum $featureName;

    public function __construct()
    {
        $this->baseUrl = config('clients.qr_pay.base_url');
        $this->channel = 'qr-pay-client';
        $this->featureName = FeatureEnum::SBP_PAY;
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::QR_PAY;
    }

    public function login(string $username, string $password): BaseQrPayResponseDTO
    {
        $params = new BaseQrPayRequestDTO(
            self::LOGIN,
            QrPayLoginRequestDTO::from(['username' => $username, 'password' => $password]),
            '2.0',
            time()
        );

        $response = Http::timeout(150)->connectTimeout(150)->post($this->baseUrl, $params);

        $this->logRequest(new HttpRequestLogDTO(
            method: 'post',
            url: $this->baseUrl,
            status: $response->status(),
            response: $response->json(),
            duration: $response->transferStats?->getTransferTime(),
            body: $params->toArray(),
        ));

        return BaseQrPayResponseDTO::from($response->json());
    }

    public function addTerminal(SbpAddTerminalRequestDTO $params): BaseQrPayResponseDTO
    {
        return $this->makeRequest(self::TERMINAL_ADD, $params);
    }

    public function updateTerminal(SbpUpdateTerminalRequestDTO $params): BaseQrPayResponseDTO
    {
        return $this->makeRequest(self::TERMINAL_UPDATE, $params);
    }

    public function deactivateTerminal(SbpDeactivateTerminalRequestDTO $params): BaseQrPayResponseDTO
    {
        return $this->makeRequest(self::TERMINAL_DEACTIVATE, $params);
    }

    public function createPayment(SbpCreatePaymentRequestDTO $params): BaseQrPayResponseDTO
    {
        return $this->makeRequest(self::PAYMENT_CREATE, $params);
    }

    public function statePayment(SbpCancelPaymentRequestDTO $params): BaseQrPayResponseDTO
    {
        return $this->makeRequest(self::PAYMENT_STATE, $params);
    }

    public function cancelPayment(SbpCancelPaymentRequestDTO $params): BaseQrPayResponseDTO
    {
        return $this->makeRequest(self::PAYMENT_CANCEL, $params);
    }

    private function httpRequest(BaseQrPayRequestDTO $data, string $httpMethod): BaseQrPayResponseDTO
    {
        if (! $token = $this->getActiveToken()) {
            return BaseQrPayResponseDTO::from([
                'error' => QrPayErrorResponseDTO::from(['error', 'Cannot activate the token']),
            ]);
        }

        $response = Http::withHeaders(['Authorization' => "Bearer {$token}"])
            ->$httpMethod($this->baseUrl, $data);

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

    private function getActiveToken(): ?string
    {
        if (! tenancy()->initialized) {
            return null;
        }

        $feature = Feature::findByName($this->featureName);

        if (! $feature->is_active || ! $feature->data) {
            return null;
        }

        $credentials = QrPayCredentialsDTO::from($feature->data);

        if ($credentials->expires_in <= time()) {
            $response = $this->login($credentials->username, $credentials->password);
            $loginResponse = QrPayLoginResponseDTO::from($response->result);
            $credentials->access_token_key = $loginResponse->access_token_key;
            $credentials->expires_in = now()->addDays(config('clients.qr_pay.validity', 365))->timestamp;
            $feature->data = $credentials->toArray();

            $feature->saveOrFail();
        }

        return $credentials->access_token_key;
    }
}
