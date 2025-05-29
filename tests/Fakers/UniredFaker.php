<?php

namespace Tests\Fakers;

use App\Clients\UniredPinflClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class UniredFaker
{
    public static function createOrUpdateCustomer(): void
    {
        $path = storage_path('../JSON-for-testing/unired/client-create-or-update.json');

        self::fakeResponse(UniredPinflClient::CONTRACT_UPDATE, $path);
    }

    public static function createContracts(): void
    {
        $path = storage_path('../JSON-for-testing/unired/contract-create.json');

        self::fakeResponse(UniredPinflClient::CONTRACT_CREATE, $path);
    }

    public static function createCustomer(): void
    {
        $path = storage_path('../JSON-for-testing/unired/processing-humo-search.json');

        self::fakeResponse(UniredPinflClient::CONTRACT_CREATE, $path);
    }

    public static function updateHumoCards(bool $isCardNotFound = false): void
    {
        if (! $isCardNotFound) {
            $path = storage_path('../JSON-for-testing/unired/processing-humo-search.json');
        } else {
            $path = storage_path('../JSON-for-testing/unired/processing-humo-search-card-not-found.json');
        }

        self::fakeResponse(UniredPinflClient::PROCESSING_HUMO_SEARCH, $path);
    }

    public static function updateUzcardCards(bool $isCardNotFound = false): void
    {
        if (! $isCardNotFound) {
            $path = storage_path('../JSON-for-testing/unired/processing-uzcard-search.json');
        } else {
            $path = storage_path('../JSON-for-testing/unired/processing-uzcard-search-card-not-found.json');
        }

        self::fakeResponse(UniredPinflClient::PROCESSING_UZCARD_SEARCH, $path);
    }

    public static function getCards(): void
    {
        $path = storage_path('../JSON-for-testing/unired/card-info.json');

        self::fakeResponse(UniredPinflClient::CARD_INFO, $path);
    }

    public static function getPayments(): void
    {
        $path = storage_path('../JSON-for-testing/unired/get-payments.json');

        self::fakeResponse(UniredPinflClient::PAYMENT_GET, $path);
    }

    public static function reversePayment(): void
    {
        $path = storage_path('../JSON-for-testing/unired/reverse-payment.json');

        self::fakeResponse(UniredPinflClient::PAYMENT_CANCEL, $path);
    }

    public static function setWebhook(): void
    {
        $path = storage_path('../JSON-for-testing/unired/set-webhook.json');

        self::fakeResponse(UniredPinflClient::SET_WEBHOOK, $path);
    }

    public static function monitoringHumo(): void
    {
        $path = storage_path('../JSON-for-testing/scoring/unired-humo-response.json');

        self::fakeResponse(UniredPinflClient::MONITORING_HUMO, $path);
    }

    public static function monitoringUzcard(): void
    {
        $partnerUrl = config('clients.unired.base_url');
        $method = UniredPinflClient::MONITORING_UZCARD;

        $dataPages = [
            0 => json_decode(file_get_contents(storage_path('../JSON-for-testing/scoring/unired-uzcard-response-1.json')), true),
            1 => json_decode(file_get_contents(storage_path('../JSON-for-testing/scoring/unired-uzcard-response-2.json')), true),
        ];

        Http::fake([
            $partnerUrl => function (Request $request) use ($dataPages, $method) {
                if ($method === $request->data()['method']) {
                    $page = $request->data()['params']['page_number'] ?? 0;

                    return Http::response($dataPages[$page] ?? $dataPages[0]);
                }

                return null;
            },
        ]);
    }

    public static function badResponse(): void
    {
        $path = storage_path('../JSON-for-testing/unired/bad-response.json');

        self::fakeResponse(null, $path);
    }

    public static function emptyResponse(): void
    {
        $path = storage_path('../JSON-for-testing/unired/empty-response.json');

        self::fakeResponse(null, $path);
    }

    public static function fakeResponse(?string $method, string $jsonPath, $status = 200): void
    {
        $partnerUrl = config('clients.unired.base_url');
        $data = json_decode(file_get_contents($jsonPath), true);

        Http::fake([
            $partnerUrl => function (Request $request) use ($data, $method, $status) {
                if (! $method) {
                    return Http::response($data, $status);
                } elseif ($method == $request->data()['method']) {
                    return Http::response($data, $status);
                } else {
                    return null;
                }
            },
        ]);
    }
}
