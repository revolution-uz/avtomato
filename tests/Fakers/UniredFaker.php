<?php

namespace Tests\Fakers;

use Illuminate\Support\Facades\Http;

class UniredFaker
{
    public static function createBranch(): void
    {
        self::fakeResponse('../JSON-for-testing/unired/filial-create.json');
    }

    public static function updateBranch(): void
    {
        self::fakeResponse('../JSON-for-testing/unired/filial-update.json');
    }

    public static function switchOffCompany(): void
    {
        self::fakeResponse('../JSON-for-testing/unired/switch-off-company.json');
    }

    public static function createOrUpdateCustomer(): void
    {
        self::fakeResponse('../JSON-for-testing/unired/client-create-or-update.json');
    }

    public static function getPayments(): void
    {
        self::fakeResponse('../JSON-for-testing/unired/get-payments.json');
    }

    public static function reversePayment(): void
    {
        self::fakeResponse('../JSON-for-testing/unired/reverse-payment.json');
    }

    public static function setWebhook(): void
    {
        self::fakeResponse('../JSON-for-testing/unired/set-webhook.json');
    }

    public static function badResponse(): void
    {
        self::fakeResponse('../JSON-for-testing/unired/bad-response.json');
    }

    public static function emptyResponse(): void
    {
        self::fakeResponse('../JSON-for-testing/unired/empty-response.json');
    }

    public static function fakeResponse(string $jsonPath, $status = 200): void
    {
        $partnerUrl = config('clients.unired.base_url');

        Http::fake([
            $partnerUrl => Http::response(json_decode(file_get_contents(storage_path($jsonPath)), true), $status),
        ]);
    }
}
