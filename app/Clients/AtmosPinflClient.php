<?php

namespace App\Clients;

use App\DTOs\Atmos\Transaction\AtmosTransactionCancelResponseDTO;
use App\DTOs\Atmos\Transaction\AtmosTransactionCreateResponseDTO;
use App\DTOs\Atmos\Transaction\AtmosTransactionInfoResponseDTO;
use App\Enums\ClientCodeEnum;
use App\Enums\FeatureEnum;
use App\Exceptions\GeneralException;
use App\Models\Company\Customer;
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

    /*
     * ------------- PINFL Payment module begin --------------
     */
    /**
     * @throws GeneralException
     */
    public function payWithPinfl(string $ext_id, Customer $customer, string $contract_number, int $amount): AtmosTransactionCreateResponseDTO
    {
        $data = [
            'ext_id' => $ext_id,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'middle_name' => $customer->middle_name,
            'pinfl' => $customer->pinfl,
            'passport_series' => $customer->passport_series,
            'passport_number' => $customer->passport_number,
            'contract_number' => $contract_number,
            'amount' => $amount * 100,
            'partial_debit' => true,
        ];

        return AtmosTransactionCreateResponseDTO::from($this->makeRequest(self::PAYMENT_CREATE, $data));

    }

    /**
     * @throws GeneralException
     */
    public function payConfirmWithPinfl(string $ext_id, string $transaction_id): AtmosTransactionInfoResponseDTO
    {
        $data = [
            'ext_id' => $ext_id,
            'transaction_id' => $transaction_id,
        ];

        return AtmosTransactionInfoResponseDTO::from($this->makeRequest(self::PAYMENT_CONFIRM, $data));
    }

    /**
     * @throws GeneralException
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
        ];

        return AtmosTransactionInfoResponseDTO::from($this->makeRequest(self::PAYMENT_CHECK, $data));
    }

    /**
     * @throws GeneralException
     */
    public function payCancelWithPinfl(string $ext_id, string $transaction_id): AtmosTransactionCancelResponseDTO
    {
        $data = [
            'ext_id' => $ext_id,
            'transaction_id' => $transaction_id,
        ];

        return AtmosTransactionCancelResponseDTO::from($this->makeRequest(self::PAYMENT_CANCEL, $data));
    }
    /*
     * ------------- PINFL Payment module end --------------
     */
}
