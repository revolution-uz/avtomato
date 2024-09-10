<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\DTOs\Unired\Base\BaseUniredDTO;
use App\DTOs\Unired\Branch\UniredToggleBranchAutoRequestDTO;
use App\DTOs\Unired\Branch\UniredCreateBranchRequestDTO;
use App\DTOs\Unired\Branch\UniredFindBranchRequestDTO;
use App\DTOs\Unired\Branch\UniredUpdateBranchRequestDTO;
use App\DTOs\Unired\Client\UniredAddClientPhoneRequestDTO;
use App\DTOs\Unired\Client\UniredCreateOrUpdateClientRequestDTO;
use App\DTOs\Unired\Client\UniredFindClientRequestDTO;
use App\DTOs\Unired\Client\UniredFindClientsRequestDTO;
use App\DTOs\Unired\Client\UniredToggleClientAutoRequestDTO;
use App\DTOs\Unired\System\UniredSetTokenRequestDTO;
use App\DTOs\Unired\UniredPaginationRequestDTO;
use App\DTOs\Unired\UniredResponseDTO;
use App\Enums\ClientCodeEnum;
use Illuminate\Support\Facades\Http;

class UniredClient extends BaseClient
{
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
        return ClientCodeEnum::ASAXIY;
    }

    public function ping(): UniredResponseDTO
    {
        return $this->httpRequest([], '', '/ping');
    }

    public function setToken(UniredSetTokenRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest('system.set.token', $params);
    }

    public function createBranch(UniredCreateBranchRequestDTO $filialData): UniredResponseDTO
    {
        return $this->makeRequest('filial.create', $filialData);
    }

    public function updateFilial(UniredUpdateBranchRequestDTO $filialData): UniredResponseDTO
    {
        return $this->makeRequest('filial.update', $filialData);
    }

    public function findFilial(UniredFindBranchRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest('filial.find', $params);
    }

    public function getAllFilials(UniredPaginationRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest('filial.all', $params);
    }

    public function toggleFilialAuto(UniredToggleBranchAutoRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest('filial.toggle.all', $params);
    }

    public function createClient(UniredCreateOrUpdateClientRequestDTO $clientData): UniredResponseDTO
    {
        return $this->makeRequest('client.create', $clientData);
    }

    public function createOrUpdateClients(UniredCreateOrUpdateClientRequestDTO $clientsData): UniredResponseDTO
    {
        return $this->makeRequest('client.createOrUpdate', $clientsData);
    }

    public function findClient(UniredFindClientRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest('client.find', $params);
    }

    public function findMultipleClients(UniredFindClientsRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest('client.find.multiple', $params);
    }

    public function addClientPhone(UniredAddClientPhoneRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest('client.phone.add', $params);
    }

    public function getAllClients(UniredPaginationRequestDTO $params): UniredResponseDTO
    {
        $params->filial_id = tenant('id');
        return $this->makeRequest('client.all', $params);
    }

    public function toggleClientAuto(UniredToggleClientAutoRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest('client.auto.toggle', $params);
    }

    // Method for registering a payment
    public function registerPayment(array $paymentData): UniredResponseDTO
    {
        return $this->makeRequest('payment.register', $paymentData);
    }

    // Method for canceling a payment
    public function cancelPayment(array $paymentData): UniredResponseDTO
    {
        return $this->makeRequest('payment.cancel', $paymentData);
    }

    // Method for checking webhook
    public function getWebhook(): UniredResponseDTO
    {
        return $this->makeRequest('payment.get.webhook', []);
    }

    // Method for setting webhook
    public function setWebhook(string $webhookUrl): UniredResponseDTO
    {
        return $this->makeRequest('payment.set.webhook', ['webhook' => $webhookUrl]);
    }

    private function httpRequest(array $data, string $httpMethod, string $path): UniredResponseDTO
    {
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->$httpMethod($this->baseUrl . $path, $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: $httpMethod . $path,
            url: $this->baseUrl,
            status: $response->status(),
            response: $response->json(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return UniredResponseDTO::from($response->json());
    }

    private function makeRequest(string $method, BaseUniredDTO $params, string $httpMethod = 'post'): UniredResponseDTO
    {
        return $this->httpRequest(['method' => $method, 'params' => $params], $httpMethod, '');
    }
}
