<?php

namespace App\Clients;

use App\DTOs\Atmos\Card\AtmosAttachCardResponseDTO;
use App\DTOs\Atmos\Card\AtmosConfirmCardResponseDTO;
use App\DTOs\Atmos\CardPay\AtmosCreatePayResponseDTO;
use App\DTOs\Atmos\CardPay\AtmosPreConfirmPayResponseDTO;
use App\Enums\ClientCodeEnum;
use App\Enums\FeatureEnum;
use App\Exceptions\AtmosException;
use Throwable;

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

    /**
     * @throws Throwable
     */
    public function __construct()
    {
        $this->baseUrl = config('clients.atmos.card.base_url');
        $this->channel = 'atmos-card-client';
        $this->featureName = FeatureEnum::ATMOS_PINFL_PAY;
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
        $response = $this->getOrRefreshToken($username, $password);
        $this->token = $response->access_token ?? null;
    }

    /*
     * ------------- Card module begin --------------
     */
    /**
     * @throws AtmosException
     */
    public function attachCard(string $card_number, string $expiry): AtmosAttachCardResponseDTO
    {
        $data = [
            'card_number' => $card_number,
            'expiry' => $expiry,
            'lang' => app()->getLocale(),
        ];

        return AtmosAttachCardResponseDTO::from($this->makeRequest(self::CARD_BIND_INIT, $data));
    }

    /**
     * @throws AtmosException
     */
    public function confirmCard(int $transaction_id, string $otp): AtmosConfirmCardResponseDTO
    {
        $data = [
            'transaction_id' => $transaction_id,
            'otp' => $otp,
            'lang' => app()->getLocale(),
        ];

        return AtmosConfirmCardResponseDTO::from($this->makeRequest(self::CARD_BIND_CONFIRM, $data));
    }

    /**
     * @throws AtmosException
     */
    public function getCardInfo(string $token): array
    {
        $data = [
            'token' => $token,
            'lang' => app()->getLocale(),
        ];

        return $this->makeRequest(self::CARD_CHECK, $data);
    }

    /**
     * @throws AtmosException
     */
    public function getCards(int $page = 0, int $pageSize = 100): array
    {
        $data = [
            'page' => $page,
            'page_size' => $pageSize,
            'lang' => app()->getLocale(),
        ];

        return $this->makeRequest(self::CARD_LIST, $data);
    }

    /**
     * @throws AtmosException
     */
    public function removeCard(string $token): array
    {
        $data = [
            'token' => $token,
            'lang' => app()->getLocale(),
        ];

        return $this->makeRequest(self::CARD_REMOVE, $data);
    }

    /*
     * ------------- Card module end --------------
     *
     * ------------- Payment module begin --------------
     */
    /**
     * @throws AtmosException
     */
    public function payCreate(string $account, int $amount, string $details): AtmosCreatePayResponseDTO
    {
        $data = [
            'store_id' => $this->store_id,
            'account' => $account,
            'amount' => $amount * 100, // tiyin
            'details' => $details,
            'debit-available-amount' => true,
            'lang' => app()->getLocale(),
        ];

        return AtmosCreatePayResponseDTO::from($this->makeRequest(self::PAYMENT_CREATE, $data));
    }

    /**
     * @throws AtmosException
     */
    public function payPreApply(string $card_token, int $transaction_id): AtmosPreConfirmPayResponseDTO
    {
        $data = [
            'store_id' => $this->store_id,
            'card_token' => $card_token,
            'transaction_id' => $transaction_id,
            'lang' => app()->getLocale(),
        ];

        return AtmosPreConfirmPayResponseDTO::from($this->makeRequest(self::PAYMENT_PRE_CONFIRM, $data));
    }

    /**
     * @throws AtmosException
     */
    public function payApplyOfd(int $transaction_id): AtmosCreatePayResponseDTO
    {
        $data = [
            'store_id' => $this->store_id,
            'transaction_id' => $transaction_id,
            'lang' => app()->getLocale(),
        ];

        return AtmosCreatePayResponseDTO::from($this->makeRequest(self::PAYMENT_CONFIRM, $data));
    }

    /**
     * @throws AtmosException
     */
    public function payGet(int $transaction_id): AtmosCreatePayResponseDTO
    {
        $data = [
            'store_id' => $this->store_id,
            'transaction_id' => $transaction_id,
            'lang' => app()->getLocale(),
        ];

        return AtmosCreatePayResponseDTO::from($this->makeRequest(self::PAYMENT_GET, $data));
    }

    /**
     * @throws AtmosException
     */
    public function payReverse(int $transaction_id, string $reason = 'Wrong payment', int $hold_amount = 0): mixed
    {
        $data = [
            'store_id' => $this->store_id,
            'transaction_id' => $transaction_id,
            'reason' => $reason,
            'hold_amount' => $hold_amount,
            'lang' => app()->getLocale(),
        ];

        return $this->makeRequest(self::PAYMENT_CANCEL, $data);
    }
    /*
     * ------------- Card Payment module end --------------
     */
}
