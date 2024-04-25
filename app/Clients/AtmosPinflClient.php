<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\Enums\ClientCodeEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\Feature;
use Illuminate\Support\Facades\Http;

class AtmosPinflClient extends BaseAtmosClient
{
    public function __construct()
    {
        $this->baseUrl = config('clients.atmos.pinfl.base_url');
        $this->setToken();
        $this->channel = 'atmos-client';
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::ATMOS;
    }

    public function setRevolutionToken(): void
    {
        $username = config('clients.atmos.pinfl.username');
        $password = config('clients.atmos.pinfl.password');
        $response = $this->getToken($username, $password);
        $this->token = $response['access_token'] ?? null;
    }

    /**
     * ------------- PINFL Payment module begin --------------
     */
    public function payWithPinfl(string $ext_id, string $first_name, string $last_name, string $middle_name, string $pinfl, string $passer, string $pasnum, string $contract_number, int $amount): mixed
    {
        $data = [
            'ext_id' => $ext_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'middle_name' => $middle_name,
            'pinfl' => $pinfl,
            'passport_series' => $passer,
            'passport_number' => $pasnum,
            'contract_number' => $contract_number,
            'amount' => $amount,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . '/ppa/transaction/create', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/ppa/transaction/create',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('payload');
    }

    public function payConfirmWithPinfl(string $ext_id, int $transaction_id): mixed
    {
        $data = [
            'ext_id' => $ext_id,
            'transaction_id' => $transaction_id,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . '/ppa/transaction/confirm', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/ppa/transaction/confirm',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('payload');
    }

    public function payCheckWithPinfl(string $ext_id, string $pinfl, string $passer, string $pasnum, string $contract_number, int $amount): mixed
    {
        $data = [
            'ext_id' => $ext_id,
            'pinfl' => $pinfl,
            'passport_series' => $passer,
            'passport_number' => $pasnum,
            'contract_number' => $contract_number,
            'amount' => $amount,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . '/ppa/transaction/check', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/ppa/transaction/check',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('payload');
    }

    public function payCancelWithPinfl(string $ext_id, int $transaction_id): mixed
    {
        $data = [
            'ext_id' => $ext_id,
            'transaction_id' => $transaction_id,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . '/ppa/transaction/cancel', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/ppa/transaction/cancel',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('payload');
    }

    private function setToken(): void
    {
        $this->activateToken(Feature::findByName(FeatureEnum::ATMOS_PINFL_PAY->value));
    }
    /**
     * ------------- PINFL Payment module end --------------
     */
}
