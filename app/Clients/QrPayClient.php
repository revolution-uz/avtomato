<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\DTOs\QrPay\Base\BaseQrPayDTO;
use App\DTOs\QrPay\Base\BaseQrPayRequestDTO;
use App\DTOs\QrPay\Base\BaseQrPayResponseDTO;
use App\DTOs\QrPay\Payment\SbpCancelPaymentRequestDTO;
use App\DTOs\QrPay\Payment\SbpCreatePaymentRequestDTO;
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
    const TRANSFER_REGISTRY = 'transfer.registry';

    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = config('clients.qr_pay.base_url');
        $this->channel = 'qr-pay-client';
        $this->setToken();
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::QR_PAY;
    }

    public function login(QrPayLoginRequestDTO $params): BaseQrPayResponseDTO
    {
        return $this->makeRequest(self::LOGIN, $params);
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
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->$httpMethod($this->baseUrl, $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: $httpMethod,
            url: $this->baseUrl,
            status: $response->status(),
            response: $response->json(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
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

    private function setToken(): void
    {
        if (! $this->token) {
            $feature = Feature::findByName(FeatureEnum::SBP_PAY);
            if (! isset($feature->data['access_token_key']) || ! $feature->data['access_token_key']) {
                $qrPayLoginResponse = $this->login(QrPayLoginRequestDTO::from($feature->data));
                $loginResponse = QrPayLoginResponseDTO::from($qrPayLoginResponse->result);
                $feature->data = array_merge($feature->data, ['access_token_key' => $loginResponse->access_token_key]);
                $feature->saveOrFail();
            } else {
                $this->token = (string) $feature->data['access_token_key'];
            }
        }
    }
}
