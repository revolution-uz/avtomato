<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\Enums\ClientCodeEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlumClient extends BaseClient
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = config('clients.plum.base_url');
        $this->token = config('clients.plum.token');
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::PLUM;
    }

    public function scoringMib(array $params)
    {
        $response = Http::withHeaders(['Authorization' => 'Basic ' . $this->token])
            ->get($this->baseUrl . '/Scoring/GetEbDebtScore?' . http_build_query($params));
        $this->logRequest(new HttpRequestLogDTO(
            method: 'GET',
            url: $this->baseUrl . '/Scoring/GetEbDebtScore',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            params: $params,
        ));

        return $response->json('result');
    }

    public function scoringTax(array $params)
    {
        $response = Http::withHeaders(['Authorization' => 'Basic ' . $this->token])
            ->get($this->baseUrl . '/Scoring/GetTaxDebtScore?' . http_build_query($params));
        $this->logRequest(new HttpRequestLogDTO(
            method: 'GET',
            url: $this->baseUrl . '/Scoring/GetTaxDebtScore',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            params: $params,
        ));

        return $response->json('result');
    }

    /**
     * @return array|mixed
     */
    public function attachCard($customer_id, $number, $expiry, $phone)
    {
        $data = [
            'userId' => $customer_id,
            'cardNumber' => $number,
            'expireDate' => $expiry,
            'userPhone' => $phone,
        ];
        $response = Http::withHeaders(['Authorization' => 'Basic ' . $this->token])
            ->post($this->baseUrl . '/UserCard/createUserCard', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/UserCard/createUserCard',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('result');
    }

    public function confirmCard(int $session, string $otp)
    {
        $data = [
            'session' => $session,
            'otp' => $otp,
            'isTrusted' => true,
        ];
        $response = Http::withHeaders(['Authorization' => 'Basic ' . $this->token])
            ->post($this->baseUrl . '/UserCard/confirmUserCardCreate', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/UserCard/confirmUserCardCreate',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('result');
    }

    /**
     * @return array|mixed
     */
    public function getCards(array $params)
    {
        $response = Http::withHeaders(['Authorization' => 'Basic ' . $this->token])
            ->get($this->baseUrl . '/UserCard/confirmUserCardCreate?' . http_build_query($params));
        $this->logRequest(new HttpRequestLogDTO(
            method: 'GET',
            url: $this->baseUrl . '/UserCard/getAllUserCards',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            params: $params,
        ));

        return $response->json('result');
    }

    protected function logRequest(HttpRequestLogDTO $request): void
    {
        Log::channel('plum-client')->info($request->url, $request->toArray());
    }
}
