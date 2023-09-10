<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;

class OilaKreditClient
{
    private const BASE_URL = 'https://api.oilakredit.uz';

    public static function getRegionsAndDistricts(string $language): array
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json, text/plain, */*',
            'Accept-Language' => $language,
            'Connection' => 'keep-alive',
            'Origin' => self::BASE_URL,
            'Referer' => self::BASE_URL . '/',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-site',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            'sec-ch-ua' => '"Not/A)Brand";v="99", "Google Chrome";v="115", "Chromium";v="115"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"',
        ])->get(self::BASE_URL . '/web/v1/public/forms/application', ['locale' => $language])->json();

        return $response['elements'][6]['children']; // [0] - regions, [1] - districts
    }

    public static function getMahallas(string $language, int $districtId): array
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json, text/plain, */*',
            'Accept-Language' => $language,
            'Connection' => 'keep-alive',
            'Origin' => self::BASE_URL,
            'Referer' => self::BASE_URL . '/',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-site',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            'sec-ch-ua' => '"Not/A)Brand";v="99", "Google Chrome";v="115", "Chromium";v="115"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"'
        ])->get(self::BASE_URL . '/api/v1/public/lists/mahalla', ['locale' => $language, 'parent_id' => $districtId])->json();

        return $response['data'];
    }
}
