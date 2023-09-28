<?php

namespace App\Clients;

use App\DTOs\OilaKredit\PassportDataDTO;
use Arr;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class OilaKreditClient
{
    private const BASE_URL = 'https://api.oilakredit.uz';
    private const HEADERS = [
        'Accept' => 'application/json, text/plain, */*',
        'Connection' => 'keep-alive',
        'Sec-Fetch-Dest' => 'empty',
        'Sec-Fetch-Mode' => 'cors',
        'Sec-Fetch-Site' => 'same-site',
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
        'sec-ch-ua' => '"Not/A)Brand";v="99", "Google Chrome";v="115", "Chromium";v="115"',
        'sec-ch-ua-mobile' => '?0',
        'sec-ch-ua-platform' => '"macOS"',
        'Origin' => self::BASE_URL,
        'Referer' => self::BASE_URL . '/',
    ];

    public static function getRegionsAndDistricts(string $language): array
    {
        $response = Http::withHeaders(array_merge(self::HEADERS, ['Accept-Language' => $language]))
            ->get(self::BASE_URL . '/web/v1/public/forms/application', ['locale' => $language])->json();

        return $response['elements'][6]['children']; // [0] - regions, [1] - districts
    }

    public static function getMahallas(string $language, int $districtId): array
    {
        $response = Http::withHeaders(array_merge(self::HEADERS, ['Accept-Language' => $language]))
            ->get(
                self::BASE_URL . '/api/v1/public/lists/mahalla',
                ['locale' => $language, 'parent_id' => $districtId]
            )->json();

        return $response['data'];
    }

    public function getDataByPassport(
        string $passportSeries,
        string $passportNumber,
        Carbon $birthDate
    ): ?PassportDataDTO
    {
        $response = Http::withHeaders(array_merge(self::HEADERS, ['Accept-Language' => app()->getLocale()]))
            ->get(
                self::BASE_URL . '/api/v1/public/tax/passport',
                [
                    'locale' => app()->getLocale(),
                    'series' => $passportSeries,
                    'number' => $passportNumber,
                    'birth_date' => $birthDate->format('Y-m-d')
                ]
            )->json();

        return Arr::has($response, 'data.info.data')
            ? PassportDataDTO::from($response['data']['info']['data'])
            : null;
    }
}
