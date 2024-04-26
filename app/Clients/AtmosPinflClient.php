<?php

namespace App\Clients;

use App\Enums\ClientCodeEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\Feature;

class AtmosPinflClient extends BaseAtmosClient
{
    const PAYMENT_CREATE = '/ppa/transaction/create';
    const PAYMENT_CONFIRM = '/ppa/transaction/confirm';
    const PAYMENT_CHECK = '/ppa/transaction/check';
    const PAYMENT_CANCEL = '/ppa/transaction/cancel';

    public function __construct()
    {
        $this->baseUrl = config('clients.atmos.pinfl.base_url');
        $this->activateToken(Feature::findByName(FeatureEnum::ATMOS_PINFL_PAY->value));
        $this->channel = 'atmos-pinfl-client';
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::ATMOS_PINFL_PAY;
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

        return $this->makeRequest(self::PAYMENT_CREATE, $data)->json('payload', []);

    }

    public function payConfirmWithPinfl(string $ext_id, int $transaction_id): mixed
    {
        $data = [
            'ext_id' => $ext_id,
            'transaction_id' => $transaction_id,
            'lang' => app()->getLocale(),
        ];

        return $this->makeRequest(self::PAYMENT_CONFIRM, $data)->json('payload', []);
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

        return $this->makeRequest(self::PAYMENT_CHECK, $data)->json('payload', []);
    }

    public function payCancelWithPinfl(string $ext_id, int $transaction_id): mixed
    {
        $data = [
            'ext_id' => $ext_id,
            'transaction_id' => $transaction_id,
            'lang' => app()->getLocale(),
        ];

        return $this->makeRequest(self::PAYMENT_CANCEL, $data)->json('payload', []);
    }
    /**
     * ------------- PINFL Payment module end --------------
     */
}
