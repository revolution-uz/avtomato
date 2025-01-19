<?php

namespace Tests\Fakers;

use App\Clients\QrPayClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class QrPayFaker
{
    public static function fake(): void
    {
        Http::fake([
            config('clients.qr_pay.base_url') => fn (Request $request) => match ($request->data()['method']) {
                QrPayClient::LOGIN => self::login(),
                QrPayClient::PAYMENT_CREATE => self::createPayment(),
                QrPayClient::PAYMENT_STATE => self::statePayment(),
            },
        ]);
    }

    private static function login()
    {
        return self::jsonResponse('login.json');
    }

    private static function createPayment()
    {
        return self::jsonResponse('payment-create.json');
    }

    private static function statePayment()
    {
        return self::jsonResponse('payment-state.json');
    }

    private static function jsonResponse(string $filename)
    {
        return Http::response(json_decode(file_get_contents(storage_path("../JSON-for-testing/qr-pay/{$filename}")), true));
    }
}
