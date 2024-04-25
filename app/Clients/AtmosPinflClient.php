<?php

namespace App\Clients;

use App\Enums\ClientCodeEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\Feature;
use Illuminate\Support\Facades\Http;

class AtmosPinflClient extends BaseAtmosClient
{
    const PAYMENT_CREATE = '/ppa/transaction/create';
    const PAYMENT_CONFIRM = '/ppa/transaction/confirm';
    const PAYMENT_CHECK = '/ppa/transaction/check';
    const PAYMENT_CANCEL = '/ppa/transaction/cancel';

    public function __construct()
    {
        $this->baseUrl = config('clients.atmos.pinfl.base_url');
        $this->setToken();
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

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . self::PAYMENT_CREATE, $data);

        $this->logAtmosRequest(self::PAYMENT_CREATE, $response, $data);

        return $response->json('payload');
    }

    public function payConfirmWithPinfl(string $ext_id, int $transaction_id): mixed
    {
        $data = [
            'ext_id' => $ext_id,
            'transaction_id' => $transaction_id,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . self::PAYMENT_CONFIRM, $data);

        $this->logAtmosRequest(self::PAYMENT_CONFIRM, $response, $data);

        return $response->json('payload');
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

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . self::PAYMENT_CHECK, $data);

        $this->logAtmosRequest(self::PAYMENT_CHECK, $response, $data);

        return $response->json('payload');
    }

    public function payCancelWithPinfl(string $ext_id, int $transaction_id): mixed
    {
        $data = [
            'ext_id' => $ext_id,
            'transaction_id' => $transaction_id,
            'lang' => app()->getLocale(),
        ];

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post($this->baseUrl . self::PAYMENT_CANCEL, $data);

        $this->logAtmosRequest(self::PAYMENT_CANCEL, $response, $data);

        return $response->json('payload');
    }

    private function setToken(): void
    {
        $this->activateToken(Feature::findByName(FeatureEnum::ATMOS_PINFL_PAY->value));
    }
    /**
     * ------------- PINFL Payment module end --------------
     */
}
