<?php

namespace App\Clients;

use Illuminate\Support\Facades\Http;

class OilaKreditClient extends BaseRealSoftClient
{
    public function getRegionsAndDistricts(string $language): array
    {
        $response = Http::withHeaders(array_merge($this->getHeaders(), ['Accept-Language' => $language]))
            ->get($this->getBaseUrl() . '/web/v1/public/forms/application', ['locale' => $language])->json();

        return $response['elements'][6]['children']; // [0] - regions, [1] - districts
    }

    public function getBaseUrl(): string
    {
        return config('clients.oila_kredit.base_url');
    }
}
