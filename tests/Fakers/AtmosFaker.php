<?php

namespace Tests\Fakers;

use Illuminate\Support\Facades\Http;

class AtmosFaker
{
    public static function attachCard()
    {
        AtmosFaker::fakeResponse('https://partner.atmos.uz/bind-card/init', '../JSON-for-testing/atmos/card/bind-card-init.json', 200);
    }

    public static function confirmCard()
    {
        self::fakeResponse('https://partner.atmos.uz/bind-card/confirm', '../JSON-for-testing/atmos/card/bind-card-confirm.json', 200);
    }

    public static function fakeResponse(string $url, string $jsonPath, $status = 200): void
    {
        Http::fake([
            $url => Http::response(json_decode(file_get_contents(storage_path($jsonPath)), true), $status),
        ]);
    }
}
