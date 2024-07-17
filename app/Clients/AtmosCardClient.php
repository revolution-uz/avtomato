<?php

namespace App\Clients;

use App\DTOs\Atmos\Card\AtmosAttachCardResponseDTO;
use App\DTOs\Atmos\Card\AtmosConfirmCardResponseDTO;
use App\DTOs\Atmos\CardPay\AtmosConfirmPaymentResponseDTO;
use App\DTOs\Atmos\CardPay\AtmosCreatePaymentResponseDTO;
use App\DTOs\Atmos\CardPay\AtmosPreConfirmPaymentResponseDTO;
use App\DTOs\Atmos\CardPay\AtmosTransactionReversePaymentDto;
use App\Enums\ClientCodeEnum;
use App\Enums\FeatureEnum;
use App\Exceptions\AtmosException;
use Throwable;

class AtmosCardClient extends BaseAtmosClient
{
    const CARD_BIND_INIT = '/partner/bind-card/init';
    const CARD_BIND_CONFIRM = '/partner/bind-card/confirm';
    const CARD_CHECK = '/card/1/check-card-token';
    const CARD_LIST = '/partner/list-cards';
    const CARD_REMOVE = '/partner/remove-card';

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
        $this->featureName = FeatureEnum::ATMOS_CARD_PAY;
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::ATMOS_CARD_PAY;
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
        ];

        return $this->makeRequest(self::CARD_LIST, $data);
    }

    /**
     * @throws AtmosException
     */
    public function removeCard(string $token): AtmosConfirmCardResponseDTO
    {
        $data = [
            'token' => $token,
        ];

        return AtmosConfirmCardResponseDTO::from($this->makeRequest(self::CARD_REMOVE, $data));
    }

    /*
     * ------------- Card module end --------------
     *
     * ------------- Payment module begin --------------
     */
    /**
     * @throws AtmosException
     */
    public function createPayment(string $account, int $amount, string $details): AtmosCreatePaymentResponseDTO
    {
        $data = [
            'store_id' => $this->store_id,
            'account' => $account,
            'amount' => $amount * 100, // tiyin
            'details' => $details,
            'debit-available-amount' => true,
        ];

        return AtmosCreatePaymentResponseDTO::from($this->makeRequest(self::PAYMENT_CREATE, $data));
    }

    /**
     * @throws AtmosException
     */
    public function preConfirmPayment(string $card_token, int $transaction_id): AtmosPreConfirmPaymentResponseDTO
    {
        $data = [
            'store_id' => $this->store_id,
            'card_token' => $card_token,
            'transaction_id' => $transaction_id,
        ];

        return AtmosPreConfirmPaymentResponseDTO::from($this->makeRequest(self::PAYMENT_PRE_CONFIRM, $data));
    }

    /**
     * @throws AtmosException
     */
    public function confirmPayment(int $transaction_id): AtmosConfirmPaymentResponseDTO
    {
        $data = [
            'store_id' => $this->store_id,
            'transaction_id' => $transaction_id,
        ];

        return AtmosConfirmPaymentResponseDTO::from($this->makeRequest(self::PAYMENT_CONFIRM, $data));
    }

    /**
     * @throws AtmosException
     */
    public function getPayment(int $transaction_id): AtmosCreatePaymentResponseDTO
    {
        $data = [
            'store_id' => $this->store_id,
            'transaction_id' => $transaction_id,
        ];

        return AtmosCreatePaymentResponseDTO::from($this->makeRequest(self::PAYMENT_GET, $data));
    }

    /**
     * @throws AtmosException
     */
    public function reversePayment(int $transaction_id, string $reason = 'Wrong payment', int $hold_amount = 0): AtmosTransactionReversePaymentDto
    {
        $data = [
            'store_id' => $this->store_id,
            'transaction_id' => $transaction_id,
            'reason' => $reason,
            'hold_amount' => $hold_amount,
        ];

        return AtmosTransactionReversePaymentDto::from($this->makeRequest(self::PAYMENT_CANCEL, $data));
    }
    /*
     * ------------- Card Payment module end --------------
     */
}
