<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
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

    // Method for pinging the server to check token validity
    public function ping(): array
    {
        return $this->httpRequest([], 'get', '/ping');
    }

    // Method for setting or updating token
    public function setToken(string $newToken): array
    {
        return $this->makeRequest('system.set.token', ['token' => $newToken]);
    }

    // Method for creating a new branch
    public function createBranch(array $filialData): array
    {
        return $this->makeRequest('filial.create', $filialData);
    }

    // Method for updating a branch
    public function updateFilial(array $filialData): array
    {
        return $this->makeRequest('/api/filial/update', $filialData);
    }

    // Method for finding a branch
    public function findFilial(string $name): array
    {
        return $this->makeRequest('/api/filial/find', ['name' => $name]);
    }

    // Method for getting all branches
    public function getAllFilials(int $pageSize, int $page): array
    {
        return $this->makeRequest('/api/filial/all', ['page_size' => $pageSize, 'page' => $page]);
    }

    // Method for toggling autopay for a branch
    public function toggleFilialAuto(int $filialId, bool $auto): array
    {
        return $this->makeRequest('/api/filial/toggle.auto', ['filial_id' => $filialId, 'auto' => $auto]);
    }

    // Method for creating a client
    public function createClient(array $clientData): array
    {
        return $this->makeRequest('/api/client/create', $clientData);
    }

    // Method for updating or creating clients in bulk
    public function createOrUpdateClients(array $clientsData): array
    {
        return $this->makeRequest('/api/client/createOrUpdate', $clientsData);
    }

    // Method for finding a client by loan_id
    public function findClient(string $loanId): array
    {
        return $this->makeRequest('/api/client/find', ['loan_id' => $loanId]);
    }

    // Method for finding multiple clients by loan_ids
    public function findMultipleClients(array $loanIds): array
    {
        return $this->makeRequest('/api/client/find.multiple', ['loan_ids' => $loanIds]);
    }

    // Method for adding a phone number to a client
    public function addClientPhone(string $pinfl, string $phone): array
    {
        return $this->makeRequest('/api/client/phone.add', ['pinfl' => $pinfl, 'phone' => $phone]);
    }

    // Method for getting all clients
    public function getAllClients(int $pageSize, int $page, ?int $filialId = null): array
    {
        $params = ['page_size' => $pageSize, 'page' => $page];
        if ($filialId) {
            $params['filial_id'] = $filialId;
        }

        return $this->makeRequest('/api/client/all', $params);
    }

    // Method for toggling autopay for a single client
    public function toggleClientAuto(string $loanId, bool $auto): array
    {
        return $this->makeRequest('/api/client/auto.toggle', ['loan_id' => $loanId, 'auto' => $auto]);
    }

    // Method for registering a payment
    public function registerPayment(array $paymentData): array
    {
        return $this->makeRequest('/api/payment/register', $paymentData);
    }

    // Method for canceling a payment
    public function cancelPayment(array $paymentData): array
    {
        return $this->makeRequest('/api/payment/cancel', $paymentData);
    }

    // Method for checking webhook
    public function getWebhook(): array
    {
        return $this->makeRequest('/api/payment/get.webhook', []);
    }

    // Method for setting webhook
    public function setWebhook(string $webhookUrl): array
    {
        return $this->makeRequest('/api/payment/set.webhook', ['webhook' => $webhookUrl]);
    }

    private function httpRequest(array $data, string $httpMethod, string $path): array
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

        return $response->json();
    }

    private function makeRequest(string $method, array $params, string $httpMethod = 'post'): array
    {
        return $this->httpRequest(['method' => $method, 'params' => $params], $httpMethod, '');
    }
}
