<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\DTOs\Royxat\CreateCreditRequestDTO;
use App\Enums\ClientCodeEnum;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\Http;

class RoyxatClient extends BaseClient
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = config('clients.royxat.base_url');
        $this->token = config('clients.royxat.token');
        $this->channel = 'royxat-client';
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::ROYXAT;
    }

    public function getCredits(string $attribute, string $attributeName = 'passport_id')
    {
        return $this->makeRequest('/search', [$attributeName => $attribute], 'GET');
    }

    public function createCredit(CreateCreditRequestDTO $creditRequestDTO)
    {
        return $this->makeRequest('', $creditRequestDTO, 'POST');
    }

    public function createCredits(array $creditRequestDTO)
    {
        return $this->makeRequest('/mass/create', ['credits' => $creditRequestDTO], 'POST');
    }

    public function updateCredit(CreateCreditRequestDTO $creditRequestDTO, string $credit_id)
    {
        return $this->makeRequest('/' . $credit_id, $creditRequestDTO, 'POST');
    }

    public function updateCredits(array $creditRequestDTO)
    {
        return $this->makeRequest('/mass/create', ['credits' => $creditRequestDTO], 'POST');
    }

    protected function makeRequest(string $path, mixed $data, string $method = 'post'): array
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Token' => $this->token,
        ])->$method($this->baseUrl . $path, $data);

        if (
            $response->status() !== 200 &&
            $response->status() !== 201
        ) {
            parent::logRequest(
                new HttpRequestLogDTO(
                    method: $method,
                    url: $this->baseUrl . $path,
                    status: $response->status(),
                    response: $response->body(),
                    duration: $response->transferStats?->getTransferTime(),
                    body: $data,
                ),
                'error'
            );

            throw new GeneralException($response['description'], $response['code']);
        }

        parent::logRequest(new HttpRequestLogDTO(
            method: $method,
            url: $this->baseUrl . $path,
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('data');
    }
}
