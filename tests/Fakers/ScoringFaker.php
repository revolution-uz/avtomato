<?php

namespace Tests\Fakers;

use Illuminate\Support\Facades\Http;

class ScoringFaker
{
    public static function royxatLoansQuery($is_failed = false): void
    {
        $url = 'https://new-api.royxat.uz/api/loans/query';

        ! $is_failed ?
            self::fakeResponse($url, '../JSON-for-testing/scoring/royxat-loans-query.json') :
            self::fakeResponse($url, '../JSON-for-testing/scoring/royxat-loans-query-failed.json');
    }

    private static function fakeResponse(string $url, string $jsonPath, $status = 200): void
    {
        Http::fake([
            $url => Http::response(json_decode(file_get_contents(storage_path($jsonPath)), true), $status),
        ]);
    }
}
