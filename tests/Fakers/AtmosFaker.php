<?php

namespace Tests\Fakers;

use App\Clients\AtmosCardClient;
use Illuminate\Support\Facades\Http;

class AtmosFaker
{
    const PARTNER_URL = 'https://partner.atmos.uz';

    public static function getToken()
    {
        self::fakeResponse(AtmosCardClient::GET_TOKEN_URI, '../JSON-for-testing/atmos/get-token-response.json');
    }

    public static function attachCard()
    {
        self::fakeResponse(AtmosCardClient::CARD_BIND_INIT, '../JSON-for-testing/atmos/card/bind-card-init.json');
    }

    public static function confirmCard()
    {
        self::fakeResponse(AtmosCardClient::CARD_BIND_CONFIRM, '../JSON-for-testing/atmos/card/bind-card-confirm.json');
    }

    public static function createPayment()
    {
        self::fakeResponse(AtmosCardClient::PAYMENT_CREATE, '../JSON-for-testing/atmos/card-pay/pay-create-response.json');
    }

    public static function preConfirmPayment()
    {
        self::fakeResponse(AtmosCardClient::PAYMENT_PRE_CONFIRM, '../JSON-for-testing/atmos/card-pay/pay-pre-confirm-response.json');
    }

    public static function confirmPayment()
    {
        self::fakeResponse(AtmosCardClient::PAYMENT_CONFIRM, '../JSON-for-testing/atmos/card-pay/pay-confirm-response.json');
    }

    public static function fakeResponse(string $path, string $jsonPath, $status = 200): void
    {
        Http::fake([
            self::PARTNER_URL . $path => Http::response(json_decode(file_get_contents(storage_path($jsonPath)), true), $status),
        ]);
    }
}
