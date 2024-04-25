<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\Enums\ClientCodeEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\Feature;
use Illuminate\Support\Facades\Http;

class AtmosCardClient extends BaseAtmosClient
{
    private int $store_id;

    public function __construct()
    {
        $this->baseUrl = config('clients.atmos.card.base_url');
        $this->setToken();
        $this->channel = 'atmos-client';
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::ATMOS;
    }

    public function setRevolutionToken(): void
    {
        $this->store_id = config('clients.atmos.card.store_id');
        $username = config('clients.atmos.card.username');
        $password = config('clients.atmos.card.password');
        $response = $this->getToken($username, $password);
        $this->token = $response['access_token'] ?? null;
    }

    /**
     * ------------- Card module begin --------------
     */
    public function attachCard($number, $expiry): mixed
    {
        $data = [
            'card_number' => $number,
            'expiry' => $expiry,
            'lang' => app()->getLocale(),
        ];
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . '/bind-card/init', $data);

        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/bind-card/init',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('result', []);
    }

    public function confirmCard(int $transaction_id, string $otp): mixed
    {
        $data = [
            'transaction_id' => $transaction_id,
            'otp' => $otp,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . '/bind-card/confirm', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/bind-card/confirm',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('result', []);
    }

    public function getCardInfo(string $token): mixed
    {
        $data = [
            'token' => $token,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . '/card/1/check-card-token', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/card/1/check-card-token',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('card', []);
    }

    public function getCards(int $page = 0, int $pageSize = 100): mixed
    {
        $data = [
            'page' => $page,
            'page_size' => $pageSize,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . '/list-cards', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/list-cards',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('card_list', []);
    }

    public function removeCard(string $token): mixed
    {
        $data = [
            'token' => $token,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . '/remove-card', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/remove-card',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('result', []);
    }

    /**
     * ------------- Card module end --------------
     */

    /**
     * ------------- Payment module begin --------------
     */
    public function payCreate(string $account, int $amount, string $details): mixed
    {
        $data = [
            'store_id' => $this->store_id,
            'account' => $account,
            'amount' => $amount, // tiyin
            'details' => $details,
            'debit-available-amount' => true,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . '/merchant/pay/create', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/merchant/pay/create',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('result');
    }

    public function payPreApply(string $card_token, int $transaction_id): mixed
    {
        $data = [
            'store_id' => $this->store_id,
            'card_token' => $card_token,
            'transaction_id' => $transaction_id,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . '/merchant/pay/pre-apply', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/merchant/pay/pre-apply',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('result');
    }

    public function payApplyOfd(int $transaction_id): mixed
    {
        $data = [
            'store_id' => $this->store_id,
            'transaction_id' => $transaction_id,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . '/merchant/pay/apply-ofd', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/merchant/pay/apply-ofd',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('result');
    }

    public function payGet(int $transaction_id): mixed
    {
        $data = [
            'store_id' => $this->store_id,
            'transaction_id' => $transaction_id,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . '/merchant/pay/get', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/merchant/pay/get',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('result');
    }

    public function payReverse(int $transaction_id, string $reason = 'Wrong payment', int $hold_amount = 0): mixed
    {
        $data = [
            'store_id' => $this->store_id,
            'transaction_id' => $transaction_id,
            'reason' => $reason,
            'hold_amount' => $hold_amount,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . '/merchant/pay/reverse', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->baseUrl . '/merchant/pay/reverse',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('result');
    }

    private function setToken(): void
    {
        $feature = Feature::findByName(FeatureEnum::ATMOS_CARD_PAY->value);
        $this->store_id = $feature->data['store_id'] ?? null;
        $this->activateToken($feature);
    }

    /**
     * ------------- Card Payment module end --------------
     */
}
