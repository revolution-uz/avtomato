<?php

namespace App\Clients;

use App\DTOs\Atmos\Transction\AtmosTransactionCancelResponseDTO;
use App\DTOs\Atmos\Transction\AtmosTransactionCreateResponseDTO;
use App\DTOs\Atmos\Transction\AtmosTransactionInfoResponseDTO;
use App\Enums\ClientCodeEnum;
use App\Enums\FeatureEnum;
use App\Exceptions\AtmosException;
use Throwable;

class AtmosPinflClient extends BaseAtmosClient
{
    const PAYMENT_CREATE = '/ppa/transaction/create';
    const PAYMENT_CONFIRM = '/ppa/transaction/confirm';
    const PAYMENT_CHECK = '/ppa/transaction/check';
    const PAYMENT_CANCEL = '/ppa/transaction/cancel';

    /**
     * @throws Throwable
     */
    public function __construct()
    {
        $this->baseUrl = config('clients.atmos.pinfl.base_url');
        $this->channel = 'atmos-pinfl-client';
        $this->featureName = FeatureEnum::ATMOS_PINFL_PAY;
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::ATMOS_PINFL_PAY;
    }

    public function setRevolutionCredentials(): void
    {
        $this->isFeature = false;
        $this->username = config('clients.atmos.pinfl.username');
        $this->password = config('clients.atmos.pinfl.password');
    }

    /*
     * ------------- PINFL Payment module begin --------------
     */
    /**
     * @throws AtmosException
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

        $res = $this->makeRequest(self::PAYMENT_CREATE, $data);

        return AtmosTransactionCreateResponseDTO::from($res['payload']);

    }

    /**
     * @throws AtmosException
     */
    public function payConfirmWithPinfl(string $ext_id, int $transaction_id): AtmosTransactionInfoResponseDTO
    {
        $data = [
            'ext_id' => $ext_id,
            'transaction_id' => $transaction_id,
            'lang' => app()->getLocale(),
        ];

        $res = $this->makeRequest(self::PAYMENT_CONFIRM, $data);

        return AtmosTransactionInfoResponseDTO::from($res['payload']);
    }

    /**
     * @throws AtmosException
     */
    public function payCheckWithPinfl(string $ext_id, string $pinfl, string $passer, string $pasnum, string $contract_number, int $amount): AtmosTransactionInfoResponseDTO
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

        $res = $this->makeRequest(self::PAYMENT_CHECK, $data);

        return AtmosTransactionInfoResponseDTO::from($res['payload']);
    }

    /**
     * @throws AtmosException
     */
    public function payCancelWithPinfl(string $ext_id, int $transaction_id): AtmosTransactionCancelResponseDTO
    {
        $data = [
            'ext_id' => $ext_id,
            'transaction_id' => $transaction_id,
            'lang' => app()->getLocale(),
        ];

        $res = $this->makeRequest(self::PAYMENT_CANCEL, $data);

        return AtmosTransactionCancelResponseDTO::from($res['payload']);
    }
    /*
     * ------------- PINFL Payment module end --------------
     */
}
