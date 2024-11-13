<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\DTOs\QrPay\Base\BaseQrPayDTO;
use App\DTOs\QrPay\Base\BaseQrPayRequestDTO;
use App\DTOs\QrPay\Base\BaseQrPayResponseDTO;
use App\DTOs\QrPay\System\QrPayLoginRequestDTO;
use App\DTOs\Unired\Branch\UniredToggleBranchAutoRequestDTO;
use App\DTOs\Unired\Payment\UniredRegisterPaymentRequestDTO;
use App\DTOs\Unired\UniredResponseDTO;
use App\Enums\ClientCodeEnum;
use App\Exceptions\QrPayException;
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
    const PAYMENT_CANCEL = 'payment.cancel';
    const TRANSFER_REGISTRY = 'transfer.registry';

    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = config('clients.qr_pay.base_url');
        $this->token = config('clients.qr_pay.token');
        $this->channel = 'qr-pay-client';
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::QR_PAY;
    }

    public function login(QrPayLoginRequestDTO $params): BaseQrPayResponseDTO
    {
        return $this->makeRequest(self::LOGIN, $params);
    }

    public function createTerminal(SpbAddTerminalRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::TERMINAL_ADD, $params);
    }

    public function updateTerminal(SpbUpdateTerminalRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::TERMINAL_UPDATE, $params);
    }

    public function toggleBranchAuto(UniredToggleBranchAutoRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::TERMINAL_DEACTIVATE, $params);
    }

    public function registerPayment(UniredRegisterPaymentRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::PAYMENT_CREATE, $params);
    }

    public function cancelPayment(SbpCancelPaymentRequestDTO $params): UniredResponseDTO
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
}
