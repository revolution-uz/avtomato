<?php

namespace App\Clients;

use App\DTOs\PassportDataDTO;
use App\Exceptions\PassportDataNotFoundException;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

abstract class BaseRealSoftClient extends BaseClient
{
    abstract public function getRegionsAndDistricts(string $language): array;

    public function getMahallas(string $language, int $districtId): array
    {
        $response = Http::withHeaders(array_merge($this->getHeaders(), ['Accept-Language' => $language]))
            ->get(
                $this->getBaseUrl() . '/api/v1/public/lists/mahalla',
                ['locale' => $language, 'parent_id' => $districtId]
            )->json();

        return $response['data'];
    }

    protected function getHeaders(): array
    {
        return [
            'Accept' => 'application/json, text/plain, */*',
            'Connection' => 'keep-alive',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-site',
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36',
            'sec-ch-ua' => '"Not/A)Brand";v="99", "Google Chrome";v="115", "Chromium";v="115"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => '"macOS"',
            'Origin' => $this->getBaseUrl(),
            'Referer' => $this->getBaseUrl() . '/',
        ];
    }

    abstract public function getBaseUrl(): string;

    /**
     * @throws PassportDataNotFoundException
     */
    public function getDataByPassport(
        string $passportSeries,
        string $passportNumber,
        Carbon $birthDate
    ): PassportDataDTO
    {
        $response = Http::withHeaders(array_merge($this->getHeaders(), ['Accept-Language' => app()->getLocale()]))
            ->get(
                $this->getBaseUrl() . '/api/v1/public/tax/passport',
                [
                    'locale' => app()->getLocale(),
                    'series' => $passportSeries,
                    'number' => $passportNumber,
                    'birth_date' => $birthDate->format('Y-m-d')
                ]
            )->json();

        if (Arr::has($response, 'data.info.data')) {
            return PassportDataDTO::from($response['data']['info']['data']);
        }

        throw new PassportDataNotFoundException();
    }
}
