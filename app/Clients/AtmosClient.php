<?php

namespace App\Clients;

use App\DTOs\HttpRequestLogDTO;
use App\Enums\ClientCodeEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\Feature;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class AtmosClient extends BaseClient
{
    private string $baseUrl;
    private string $gwUrl;

    private string $token;
    private int $store_id;

    public function __construct(FeatureEnum $account_type = null)
    {
        $this->baseUrl = config('clients.atmos.base_url');
        $this->gwUrl = config('clients.atmos.gw_url');
        if ($account_type == null) {
            $this->setRevolutionToken();
        } else {
            $this->setAndActivateToken($account_type);
        }
        $this->channel = 'atmos-client';
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::ATMOS;
    }

    public function activateAccount($basUrl, $username, $password)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode($username . ':' . $password),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])
            ->get($basUrl . '/token?grant_type=client_credentials');
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $basUrl . '/token?grant_type=client_credentials',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            params: [],
        ));

        return $response->json();
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

    /**
     * ------------- Card Payment module end --------------
     */

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
            ->post($this->gwUrl . '/ppa/transaction/create', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->gwUrl . '/ppa/transaction/create',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

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
            ->post($this->gwUrl . '/ppa/transaction/confirm', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->gwUrl . '/ppa/transaction/confirm',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

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
            ->post($this->gwUrl . '/ppa/transaction/check', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->gwUrl . '/ppa/transaction/check',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

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
            ->post($this->gwUrl . '/ppa/transaction/cancel', $data);
        $this->logRequest(new HttpRequestLogDTO(
            method: 'POST',
            url: $this->gwUrl . '/ppa/transaction/cancel',
            status: $response->status(),
            response: $response->body(),
            duration: $response->transferStats?->getTransferTime(),
            body: $data,
        ));

        return $response->json('payload');
    }

    private function setRevolutionToken(): void
    {
        $this->store_id = config('clients.atmos.revolution.store_id');
        $username = config('clients.atmos.revolution.username');
        $password = config('clients.atmos.revolution.password');
        $response = $this->activateAccount($this->baseUrl, $username, $password);
        $this->token = $response['access_token'] ?? null;
    }

    private function setAndActivateToken($account_type): void
    {
        $feature = Feature::findByName($account_type);
        $this->store_id = $feature->data['store_id'] ?? null;
        $username = $feature->data['username'] ?? null;
        $password = $feature->data['password'] ?? null;
        $expires_in = $feature->data['expires_in'] ?? 0;
        $this->token = $feature->data['token'] ?? null;
        if ($expires_in > Carbon::now() && $this->token) {
            return;
        }
        $baseUrl = $this->baseUrl;
        if ($account_type == FeatureEnum::ATMOS_PINFL_PAY) {
            $baseUrl = $this->gwUrl;
        }
        $response = $this->activateAccount($baseUrl, $username, $password);
        $this->token = $response['access_token'] ?? null;
        $expires_in = ($response['expires_in'] ?? 0) + Carbon::now();
        $feature->data['token'] = $this->token;
        $feature->data['expires_in'] = $expires_in;
        $feature->saveOrFail();
    }

    /**
     * ------------- PINFL Payment module end --------------
     */
}
