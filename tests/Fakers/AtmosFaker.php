<?php

namespace Tests\Fakers;

use App\Clients\AtmosCardClient;
use Illuminate\Support\Facades\Http;

class AtmosFaker
{
    const PARTNER_URL = 'https://partner.atmos.uz';

    public static function attachCard()
    {
        AtmosFaker::fakeResponse(self::PARTNER_URL . AtmosCardClient::CARD_BIND_INIT, '../JSON-for-testing/atmos/card/bind-card-init.json', 200);
    }

    public static function confirmCard()
    {
        self::fakeResponse(self::PARTNER_URL . AtmosCardClient::CARD_BIND_CONFIRM, '../JSON-for-testing/atmos/card/bind-card-confirm.json', 200);
    }

    public static function createPayment()
    {
        self::fakeResponse(self::PARTNER_URL . AtmosCardClient::PAYMENT_CREATE, '../JSON-for-testing/atmos/card-pay/pay-create-response.json', 200);
    }

    public static function preConfirmPayment()
    {
        self::fakeResponse(self::PARTNER_URL . AtmosCardClient::PAYMENT_PRE_CONFIRM, '../JSON-for-testing/atmos/card-pay/pay-pre-confirm-response.json', 200);
    }

    public static function confirmPayment()
    {
        self::fakeResponse(self::PARTNER_URL . AtmosCardClient::PAYMENT_CONFIRM, '../JSON-for-testing/atmos/card-pay/pay-confirm-response.json', 200);
    }

    public static function fakeResponse(string $url, string $jsonPath, $status = 200): void
    {
        Http::fake([
            $url => Http::response(json_decode(file_get_contents(storage_path($jsonPath)), true), $status),
        ]);
    }
}
