<?php

namespace Tests\Fakers;

use App\Clients\AtmosPinflClient;
use Illuminate\Support\Facades\Http;

class AtmosApigwFaker
{
    const string APIGW_URL = 'https://apigw.atmos.uz';

    public static function getToken(): void
    {
        self::fakeResponse(AtmosPinflClient::GET_TOKEN_URI, '../JSON-for-testing/atmos/get-token-response.json');
    }

    public static function createPayment($is_failed = false): void
    {
        if (! $is_failed) {
            self::fakeResponse(AtmosPinflClient::PAYMENT_CREATE, '../JSON-for-testing/atmos/pinfl-pay/pay-create-response.json');
        } else {
            self::fakeResponse(AtmosPinflClient::PAYMENT_CREATE, '../JSON-for-testing/atmos/pinfl-pay/pay-create-failed-response.json');
        }
    }

    public static function confirmPayment(): void
    {
        self::fakeResponse(AtmosPinflClient::PAYMENT_CONFIRM, '../JSON-for-testing/atmos/pinfl-pay/pay-confirm-response.json');
    }

    public static function fakeResponse(string $path, string $jsonPath, $status = 200): void
    {
        Http::fake([
            self::APIGW_URL . $path => Http::response(json_decode(file_get_contents(storage_path($jsonPath)), true), $status),
        ]);
    }
}
