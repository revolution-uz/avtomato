<?php

namespace App\Clients;

use App\Enums\ClientCodeEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\Feature;
use Illuminate\Support\Facades\Http;

class AtmosCardClient extends BaseAtmosClient
{
    const CARD_BIND_INIT = '/bind-card/init';
    const CARD_BIND_CONFIRM = '/bind-card/confirm';
    const CARD_CHECK = '/card/1/check-card-token';
    const CARD_LIST = '/list-cards';
    const CARD_REMOVE = '/remove-card';

    const PAYMENT_CREATE = '/merchant/pay/create';
    const PAYMENT_PRE_CONFIRM = '/merchant/pay/pre-apply';
    const PAYMENT_CONFIRM = '/merchant/pay/apply-ofd';
    const PAYMENT_CANCEL = '/merchant/pay/reverse';
    const PAYMENT_GET = '/merchant/pay/get';

    private int $store_id;

    public function __construct()
    {
        $this->baseUrl = config('clients.atmos.card.base_url');
        $this->setToken();
        $this->channel = 'atmos-card-client';
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::ATMOS_CARD_PAY;
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
            ->post($this->baseUrl . self::CARD_BIND_INIT, $data);

        $this->logAtmosRequest(self::CARD_BIND_INIT, $response, $data);

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
            ->post($this->baseUrl . self::CARD_BIND_CONFIRM, $data);

        $this->logAtmosRequest(self::CARD_BIND_CONFIRM, $response, $data);

        return $response->json('result', []);
    }

    public function getCardInfo(string $token): mixed
    {
        $data = [
            'token' => $token,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . self::CARD_CHECK, $data);

        $this->logAtmosRequest(self::CARD_CHECK, $response, $data);

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
            ->post($this->baseUrl . self::CARD_LIST, $data);

        $this->logAtmosRequest(self::CARD_LIST, $response, $data);

        return $response->json('card_list', []);
    }

    public function removeCard(string $token): mixed
    {
        $data = [
            'token' => $token,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . self::CARD_REMOVE, $data);

        $this->logAtmosRequest(self::CARD_REMOVE, $response, $data);

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
            ->post($this->baseUrl . self::PAYMENT_CREATE, $data);

        $this->logAtmosRequest(self::PAYMENT_CREATE, $response, $data);

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
            ->post($this->baseUrl . self::PAYMENT_PRE_CONFIRM, $data);

        $this->logAtmosRequest(self::PAYMENT_PRE_CONFIRM, $response, $data);

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
            ->post($this->baseUrl . self::PAYMENT_CONFIRM, $data);

        $this->logAtmosRequest(self::PAYMENT_CONFIRM, $response, $data);

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
            ->post($this->baseUrl . self::PAYMENT_GET, $data);

        $this->logAtmosRequest(self::PAYMENT_GET, $response, $data);

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
            ->post($this->baseUrl . self::PAYMENT_CANCEL, $data);

        $this->logAtmosRequest(self::PAYMENT_CANCEL, $response, $data);

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
