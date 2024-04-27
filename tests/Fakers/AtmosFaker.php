<?php

namespace Tests\Fakers;

use Illuminate\Support\Facades\Http;

class AtmosFaker
{
    public static function fakeResponse(string $url, string $jsonPath, $status = 200): void
    {
        Http::fake([
            $url => Http::response(json_decode(file_get_contents(storage_path($jsonPath)), true), $status),
        ]);
    }
}
