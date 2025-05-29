<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\DTOs\Unired\Client\UniredAddClientPhonesRequestDTO;
use App\DTOs\Unired\Client\UniredClientCardRegisterRequestDTO;
use App\DTOs\Unired\Client\UniredClientCardVerifyRequestDTO;
use App\DTOs\Unired\Client\UniredClientMonitoringCardRequestDTO;
use App\DTOs\Unired\Client\UniredClientPinflCardUpdateRequestDTO;
use App\DTOs\Unired\Client\UniredClientPinflGetCardRequestDTO;
use App\DTOs\Unired\Client\UniredClientPinflGetCardUpdatesRequestDTO;
use App\DTOs\Unired\Client\UniredContractLoanIdDTO;
use App\DTOs\Unired\Client\UniredCreateClientRequestDTO;
use App\DTOs\Unired\Client\UniredToggleClientAutoRequestDTO;
use App\DTOs\Unired\Contract\UniredCreateContractRequestDTO;
use App\DTOs\Unired\Contract\UniredUpdateContractRequestDTO;
use App\DTOs\Unired\Payment\UniredCancelPaymentRequestDTO;
use App\DTOs\Unired\Payment\UniredGetPaymentsPaginationRequestDTO;
use App\DTOs\Unired\System\UniredSetTokenRequestDTO;
use App\DTOs\Unired\System\UniredSetWebhookDTO;
use App\DTOs\Unired\UniredResponseDTO;
use App\Enums\ClientCodeEnum;
use App\Exceptions\UniredException;
use Illuminate\Support\Facades\Http;

class UniredPinflClient extends BaseClient
{
    // Authorization Module
    const SYSTEM_SET_TOKEN = 'system.set.token';

    // Client Module
    const CLIENT_CREATE = 'client.create';
    const CLIENT_AUTO_TOGGLE = 'client.auto.toggle';
    const ADD_CLIENT_PHONE = 'client.phone.add';

    // Payment Module
    const PAYMENT_GET = 'payment.get';
    const PAYMENT_CANCEL = 'transaction.cancel';
    const CHECK_WEBHOOK = 'webhook.check';
    const SET_WEBHOOK = 'webhook.set';

    const CONTRACT_UPDATE = 'contract.update';
    const CONTRACT_CREATE = 'contract.create';
    const CONTRACT_DELETE = 'contract.delete';
    const TRANSACTION_GET = 'transaction.get';
    const CARD_INFO = 'card.info';
    const PROCESSING_HUMO_SEARCH = 'processing.humo.search';
    const PROCESSING_UZCARD_SEARCH = 'processing.uzcard.search';
    const PROCESSING_GET_HISTORY = 'processing.get.history';
    const CARD_REGISTER = 'card.register';
    const CARD_VERIFY = 'card.verify';
    const MONITORING_HUMO = 'monitoring.humo';
    const MONITORING_UZCARD = 'monitoring.uzcard';

    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = config('clients.unired.pinfl.base_url');
        $this->token = config('clients.unired.pinfl.token');
        $this->channel = 'unired-client';
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::UNIRED;
    }

    public function ping(): UniredResponseDTO
    {
        return $this->httpRequest([], '', '/ping');
    }

    public function setToken(UniredSetTokenRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::SYSTEM_SET_TOKEN, $params->toArray());
    }

    public function toggleClientAuto(UniredToggleClientAutoRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_AUTO_TOGGLE, $params->toArray());
    }

    public function getPayments(UniredGetPaymentsPaginationRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::PAYMENT_GET, $params->toArray());
    }

    public function cancelPayment(UniredCancelPaymentRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::PAYMENT_CANCEL, $params->toArray());
    }

    public function checkWebhook(): UniredResponseDTO
    {
        return $this->makeRequest(self::CHECK_WEBHOOK, []);
    }

    public function setWebhook(UniredSetWebhookDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::SET_WEBHOOK, $params->toArray());
    }

    public function createClients(UniredCreateClientRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_CREATE, [
            'clients' => [$params->toArray()],
        ]);
    }

    public function addClientPhones(UniredAddClientPhonesRequestDTO $params)
    {
        return $this->makeRequest(self::ADD_CLIENT_PHONE, [
            'clients' => [$params->toArray()],
        ]);
    }

    public function createContracts(UniredCreateContractRequestDTO $params)
    {
        return $this->makeRequest(self::CONTRACT_CREATE, [
            'contracts' => [$params->toArray()],
        ]);
    }

    public function updateContract(UniredUpdateContractRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CONTRACT_UPDATE, $params->toArray());
    }

    public function deleteContract(UniredContractLoanIdDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CONTRACT_DELETE, $params->toArray());
    }

    public function getTransactions(UniredGetPaymentsPaginationRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::TRANSACTION_GET, $params->toArray());
    }

    public function getCards(UniredClientPinflGetCardRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CARD_INFO, $params->toArray());
    }

    public function updateHumoCards(UniredClientPinflCardUpdateRequestDTO $params)
    {
        return $this->makeRequest(self::PROCESSING_HUMO_SEARCH, $params->toArray());
    }

    public function updateUzcardCards(UniredClientPinflCardUpdateRequestDTO $params)
    {
        return $this->makeRequest(self::PROCESSING_UZCARD_SEARCH, $params->toArray());
    }

    public function cardRegister(UniredClientCardRegisterRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CARD_REGISTER, $params->toArray());
    }

    public function cardVerify(UniredClientCardVerifyRequestDTO $params)
    {
        return $this->makeRequest(self::CARD_VERIFY, $params->toArray());
    }

    public function monitoringHumo(UniredClientMonitoringCardRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::MONITORING_HUMO, $params->toArray());
    }

    public function monitoringUzcard(UniredClientMonitoringCardRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::MONITORING_UZCARD, $params->toArray());
    }

    public function getCardUpdates(UniredClientPinflGetCardUpdatesRequestDTO $params, int $page): UniredResponseDTO
    {
        return $this->makeRequest(
            self::PROCESSING_GET_HISTORY,
            ['page_size' => 500, 'page_number' => $page, 'search' => $params->toArray()]
        );
    }

    private function httpRequest(array $data, string $httpMethod, string $path): UniredResponseDTO
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ];
        $response = Http::withHeaders($headers)
            ->timeout(150)
            ->connectTimeout(150)
            ->$httpMethod($this->baseUrl . $path, $data);
        $headers['Authorization'] = 'Bearer ****' . substr($this->token, -9);
        $this->logRequest(new HttpRequestLogDTO(
            method: $httpMethod,
            url: $this->baseUrl . $path,
            status: $response->status(),
            response: $response->json(),
            duration: $response->transferStats?->getTransferTime(),
            headers: $headers,
            body: $data,
        ));

        sleep(1);

        return UniredResponseDTO::from($response->json());
    }

    private function makeRequest($method, $params, $httpMethod = 'post'): UniredResponseDTO
    {
        $response = $this->httpRequest(['method' => $method, 'params' => $params], $httpMethod, '');

        if (! $response->isSuccess()) {
            throw new UniredException($response->error->message);
        }

        return $response;
    }
}
