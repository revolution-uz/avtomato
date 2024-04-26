<?php

namespace App\Clients;

use App\DTOs\Atmos\Card\AtmosAttachCardRequestDTO;
use App\DTOs\Atmos\Card\AtmosConfirmCardRequestDTO;
use App\DTOs\Atmos\CardPay\AtmosConfirmPayRequestDTO;
use App\DTOs\Atmos\CardPay\AtmosCreatePayRequestDTO;
use App\DTOs\Atmos\CardPay\AtmosPreConfirmPayRequestDTO;
use App\Enums\ClientCodeEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\Feature;

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

    public function __construct()
    {
        $this->baseUrl = config('clients.atmos.card.base_url');
        $this->activateToken(Feature::findByName(FeatureEnum::ATMOS_CARD_PAY->value));
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
    public function attachCard(AtmosAttachCardRequestDTO $card): mixed
    {
        $data = [
            'card_number' => $card->card_number,
            'expiry' => $card->expiry,
            'lang' => app()->getLocale(),
        ];

        return $this->makeRequest(self::CARD_BIND_INIT, $data)->json();
    }

    public function confirmCard(AtmosConfirmCardRequestDTO $confirmCardDTO): mixed
    {
        $data = [
            'transaction_id' => $confirmCardDTO->transaction_id,
            'otp' => $confirmCardDTO->otp,
            'lang' => app()->getLocale(),
        ];

        return $this->makeRequest(self::CARD_BIND_CONFIRM, $data)->json();
    }

    public function getCardInfo(string $token): mixed
    {
        $data = [
            'token' => $token,
            'lang' => app()->getLocale(),
        ];

        return $this->makeRequest(self::CARD_CHECK, $data)->json();
    }

    public function getCards(int $page = 0, int $pageSize = 100): mixed
    {
        $data = [
            'page' => $page,
            'page_size' => $pageSize,
            'lang' => app()->getLocale(),
        ];

        return $this->makeRequest(self::CARD_LIST, $data)->json();
    }

    public function removeCard(string $token): mixed
    {
        $data = [
            'token' => $token,
            'lang' => app()->getLocale(),
        ];

        return $this->makeRequest(self::CARD_REMOVE, $data)->json();
    }

    /**
     * ------------- Card module end --------------
     */

    /**
     * ------------- Payment module begin --------------
     */
    public function payCreate(AtmosCreatePayRequestDTO $createPayRequestDTO): mixed
    {
        $data = [
            'store_id' => $this->store_id,
            'account' => $createPayRequestDTO->account,
            'amount' => (int) $createPayRequestDTO->amount * 100, // tiyin
            'details' => $createPayRequestDTO->details,
            'debit-available-amount' => true,
            'lang' => app()->getLocale(),
        ];

        return $this->makeRequest(self::PAYMENT_CREATE, $data)->json();
    }

    public function payPreApply(AtmosPreConfirmPayRequestDTO $preConfirmPayRequestDTO): mixed
    {
        $data = [
            'store_id' => $this->store_id,
            'card_token' => $preConfirmPayRequestDTO->card_token,
            'transaction_id' => $preConfirmPayRequestDTO->transaction_id,
            'lang' => app()->getLocale(),
        ];

        return $this->makeRequest(self::PAYMENT_PRE_CONFIRM, $data)->json();
    }

    public function payApplyOfd(AtmosConfirmPayRequestDTO $confirmPayRequestDTO): mixed
    {
        $data = [
            'store_id' => $this->store_id,
            'transaction_id' => $confirmPayRequestDTO->transaction_id,
            'lang' => app()->getLocale(),
        ];

        return $this->makeRequest(self::PAYMENT_CONFIRM, $data)->json();
    }

    public function payGet(int $transaction_id): mixed
    {
        $data = [
            'store_id' => $this->store_id,
            'transaction_id' => $transaction_id,
            'lang' => app()->getLocale(),
        ];

        return $this->makeRequest(self::PAYMENT_GET, $data)->json();
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

        return $this->makeRequest(self::PAYMENT_CANCEL, $data)->json();
    }
    /**
     * ------------- Card Payment module end --------------
     */
}
