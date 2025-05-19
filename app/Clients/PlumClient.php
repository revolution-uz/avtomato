<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\Enums\ClientCodeEnum;
use App\Models\Company\BalanceFund;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\Fakers\PlumFaker;

class PlumClient extends BaseClient
{
    private string $baseUrl;
    private string $token;

    public function __construct()
    {
        $this->baseUrl = config('clients.plum.base_url');
        $this->token = config('clients.plum.token');
        $this->channel = 'plum-client';
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
    public function attachCard($customer_id, $number, $expiry, $phone): mixed
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

    public function confirmCard(int $session, string $otp): mixed
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
    public function getCards(array $params): mixed
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

    public function payment($data)
    {
        $extraId = Str::uuid();
        $data['extraId'] = $extraId;
        PlumFaker::fakeResponseWithoutURL('../JSON-for-testing/balance-funding-response.json', 200, 'POST');

        $response = Http::withHeaders(['Authorization' => 'Basic ' . $this->token])
            ->post($this->baseUrl . '/Payment/paymentWithoutRegistration', $data);

        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/Payment/paymentWithoutRegistration',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        if (! $response->json('error')) {
            $fundBalance = $response->json('result');
            $fundBalance['amount'] = $data['amount'];

            BalanceFund::query()->create([
                'amount' => $fundBalance['amount'],
                'phone_number' => $fundBalance['otpSentPhone'],
                'plum_transaction_id' => $fundBalance['transactionId'],
            ]);

            return $fundBalance;
        } else {
            return false;
        }
    }

    public function confirmPayment($data)
    {
        PlumFaker::fakeResponseWithoutURL('../JSON-for-testing/confirm-payment-of-balance-funding-response.json', 200, 'POST');

        $response = Http::withHeaders(['Authorization' => 'Basic ' . $this->token])
            ->post($this->baseUrl . '/Payment/confirmPayment', $data);
        if (! $response->json('error')) {
            $confirmPayment = $response->json('result');

            BalanceFund::query()->where('plum_transaction_id', $confirmPayment['transactionId'])
                ->firstOrFail()
                ->update([
                    'terminal_id' => $confirmPayment['terminalId'],
                    'merchant_id' => $confirmPayment['merchantId'],
                    'card_number' => $confirmPayment['cardNumber'],
                ]);

            $this->logRequest(new HttpRequestLogDTO(
                method: 'POST',
                url: $this->baseUrl . '/Payment/confirmPayment',
                status: $response->status(),
                response: $response->body(),
                duration: $response->transferStats?->getTransferTime(),
                body: $data,
            ));

            return $response->json('result');
        } else {
            return false;
        }
    }
}
