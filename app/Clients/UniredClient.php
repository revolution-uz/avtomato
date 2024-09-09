<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\DTOs\Unired\Base\BaseUniredDTO;
use App\DTOs\Unired\Branch\ToggleBranchAutoRequestDTO;
use App\DTOs\Unired\Branch\UniredCreateBranchRequestDTO;
use App\DTOs\Unired\Branch\UniredFindBranchRequestDTO;
use App\DTOs\Unired\Branch\UniredUpdateBranchRequestDTO;
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

    public function toggleFilialAuto(ToggleBranchAutoRequestDTO $params): UniredResponseDTO
    {
        return $this->makeRequest('filial.toggle.all', $params);
    }

    public function createClient(array $clientData): UniredResponseDTO
    {
        return $this->makeRequest('client.create', $clientData);
    }

    // Method for updating or creating clients in bulk
    public function createOrUpdateClients(array $clientsData): UniredResponseDTO
    {
        return $this->makeRequest('client.createOrUpdate', $clientsData);
    }

    // Method for finding a client by loan_id
    public function findClient(string $loanId): array
    {
        return $this->makeRequest('client.find', ['loan_id' => $loanId]);
    }

    // Method for finding multiple clients by loan_ids
    public function findMultipleClients(array $loanIds): array
    {
        return $this->makeRequest('client.find.multiple', ['loan_ids' => $loanIds]);
    }

    // Method for adding a phone number to a client
    public function addClientPhone(string $pinfl, string $phone): array
    {
        return $this->makeRequest('client.phone.add', ['pinfl' => $pinfl, 'phone' => $phone]);
    }

    // Method for getting all clients
    public function getAllClients(int $pageSize, int $page, ?int $filialId = null): array
    {
        $params = ['page_size' => $pageSize, 'page' => $page];
        if ($filialId) {
            $params['filial_id'] = $filialId;
        }

        return $this->makeRequest('client.all', $params);
    }

    // Method for toggling autopay for a single client
    public function toggleClientAuto(string $loanId, bool $auto): array
    {
        return $this->makeRequest('client.auto.toggle', ['loan_id' => $loanId, 'auto' => $auto]);
    }

    // Method for registering a payment
    public function registerPayment(array $paymentData): array
    {
        return $this->makeRequest('payment.register', $paymentData);
    }

    // Method for canceling a payment
    public function cancelPayment(array $paymentData): array
    {
        return $this->makeRequest('payment.cancel', $paymentData);
    }

    // Method for checking webhook
    public function getWebhook(): array
    {
        return $this->makeRequest('payment.get.webhook', []);
    }

    // Method for setting webhook
    public function setWebhook(string $webhookUrl): array
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
