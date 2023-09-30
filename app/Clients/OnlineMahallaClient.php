<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;

class OnlineMahallaClient extends BaseRealSoftClient
{
    public function getRegionsAndDistricts(string $language): array
    {
        $response = Http::withHeaders(array_merge($this->getHeaders(), ['Accept-Language' => $language]))
            ->get($this->getBaseUrl() . '/web/v1/public/forms/subsidy', ['locale' => $language])->json();

        return $response['elements'][4]['children']; // [0] - regions, [1] - districts
    }

    public function getBaseUrl(): string
    {
        return config('clients.online_mahalla.base_url');
    }
}
