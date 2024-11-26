<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\DTOs\Unired\Branch\UniredCreateBranchRequestDTO;
use App\DTOs\Unired\Branch\UniredToggleBranchAutoRequestDTO;
use App\DTOs\Unired\Branch\UniredUpdateBranchRequestDTO;
use App\DTOs\Unired\Client\UniredAddClientPhoneRequestDTO;
use App\DTOs\Unired\Client\UniredClientExtIdDTO;
use App\DTOs\Unired\Client\UniredCreateOrUpdateClientRequestDTO;
use App\DTOs\Unired\Client\UniredFindClientRequestDTO;
use App\DTOs\Unired\Client\UniredFindClientsRequestDTO;
use App\DTOs\Unired\Client\UniredToggleClientAutoRequestDTO;
use App\DTOs\Unired\Client\UniredUpdateClientDebitRequestDTO;
use App\DTOs\Unired\Payment\UniredCancelPaymentRequestDTO;
use App\DTOs\Unired\Payment\UniredChangePaymentSendStatusRequestDTO;
use App\DTOs\Unired\Payment\UniredGetPaymentsPaginationRequestDTO;
use App\DTOs\Unired\Payment\UniredRegisterPaymentRequestDTO;
use App\DTOs\Unired\System\UniredSetTokenRequestDTO;
use App\DTOs\Unired\System\UniredSetWebhookDTO;
use App\DTOs\Unired\UniredPaginationRequestDTO;
use App\DTOs\Unired\UniredResponseDTO;
use App\Enums\ClientCodeEnum;
use App\Exceptions\UniredException;
use Illuminate\Support\Facades\Http;

class UniredClient extends BaseClient
{
    // Authorization Module
    const SYSTEM_SET_TOKEN = 'system.set.token';

    // Branch Module
    const FILIAL_CREATE = 'filial.create';
    const FILIAL_UPDATE = 'filial.update';
    const FILIAL_TOGGLE_AUTO = 'filial.toggle.auto';

    // Client Module
    const CLIENT_CREATE = 'client.create';
    const CLIENT_CREATE_OR_UPDATE = 'client.createOrUpdate';
    const CLIENT_DEBIT_UPDATE = 'client.debit.update';
    const CLIENT_FIND = 'client.find';
    const CLIENT_FIND_MULTIPLE = 'client.find.multiple';
    const CLIENT_PHONE_ADD = 'client.phone.add';
    const CLIENT_ALL = 'client.all';
    const CLIENT_AUTO_TOGGLE = 'client.auto.toggle';
    const CLIENT_DELETE = 'client.delete';

    // Payment Module
    const PAYMENT_GET = 'payment.get';
    const PAYMENT_CHANGE_SEND_STATUS = 'payment.change.send.status';
    const PAYMENT_REGISTER = 'payment.register';
    const PAYMENT_CANCEL = 'payment.cancel';
    const PAYMENT_GET_WEBHOOK = 'payment.get.webhook';
    const PAYMENT_SET_WEBHOOK = 'payment.set.webhook';

    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = config('clients.unired.base_url');
        $this->token = config('clients.unired.token');
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

    public function createBranch(UniredCreateBranchRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::FILIAL_CREATE, $params->toArray());
    }

    public function updateBranch(UniredUpdateBranchRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::FILIAL_UPDATE, $params->toArray());
    }

    public function toggleBranchAuto(UniredToggleBranchAutoRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::FILIAL_TOGGLE_AUTO, $params->toArray());
    }

    public function createClient(UniredCreateOrUpdateClientRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_CREATE, $params->toArray());
    }

    public function createOrUpdateClients(UniredCreateOrUpdateClientRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_CREATE_OR_UPDATE, [$params->toArray()]);
    }

    public function updateClientDebit(UniredUpdateClientDebitRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_DEBIT_UPDATE, $params->toArray());
    }

    public function findClient(UniredFindClientRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_FIND, $params->toArray());
    }

    public function findMultipleClients(UniredFindClientsRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_FIND_MULTIPLE, $params->toArray());
    }

    public function addClientPhone(UniredAddClientPhoneRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_PHONE_ADD, $params->toArray());
    }

    public function getAllClients(UniredPaginationRequestDTO $params): UniredResponseDTO
    {
        $params->filial_id = tenant('id');

        return $this->makeRequest(self::CLIENT_ALL, $params->toArray());
    }

    public function toggleClientAuto(UniredToggleClientAutoRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_AUTO_TOGGLE, $params->toArray());
    }

    public function deleteClient(UniredClientExtIdDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::CLIENT_DELETE, $params->toArray());
    }

    public function getPayments(UniredGetPaymentsPaginationRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::PAYMENT_GET, $params->toArray());
    }

    public function registerPayment(UniredRegisterPaymentRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::PAYMENT_REGISTER, $params->toArray());
    }

    public function cancelPayment(UniredCancelPaymentRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::PAYMENT_CANCEL, $params->toArray());
    }

    public function changePaymentSendStatus(UniredChangePaymentSendStatusRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::PAYMENT_CHANGE_SEND_STATUS, $params->toArray());
    }

    public function getWebhook(): UniredResponseDTO
    {
        return $this->makeRequest(self::PAYMENT_GET_WEBHOOK, []);
    }

    public function setWebhook(UniredSetWebhookDTO $params): UniredResponseDTO
    {
        return $this->makeRequest(self::PAYMENT_SET_WEBHOOK, $params->toArray());
    }

    private function httpRequest(array $data, string $httpMethod, string $path): UniredResponseDTO
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->token,
        ];
        $response = Http::withHeaders($headers)->$httpMethod($this->baseUrl . $path, $data);
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
