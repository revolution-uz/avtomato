<?php

namespace Tests\Fakers;

use Illuminate\Support\Facades\Http;

class PlumFaker
{
    public static function attachUzcard()
    {
        Http::fake([
            'pay.myuzcard.uz/*' => Http::response([
                'result' => [
                    'session' => 1,
                    'otpSentPhone' => '+998901234567',
                ],
            ]),
        ]);
    }

    public static function confirmCard($card_id)
    {
        Http::fake([
            'pay.myuzcard.uz/*' => Http::response([
                'result' => [
                    'card' => [
                        'id' => $card_id,
                    ],
                ],
            ]),
        ]);
    }

    public static function get_info_by_passport($name)
    {
        Http::fake([
            'https://api.oilakredit.uz/*' => Http::response([
                'data' => [
                    'info' => [
                        'data' => [
                            'pinfl' => '12345678901234',
                            'tin' => '123456789',
                            'series' => 'AA',
                            'number' => '1234567',
                            'sur_name' => 'John',
                            'name' => $name,
                            'patronymic_name' => 'Alex',
                            'full_name' => 'John Doe Alex',
                            'birth_date' => '1996-01-01',
                            'birth_place' => 'Urganch shahar',
                            'given_date' => '2021-01-01',
                            'expiration_date' => '2031-01-01',
                            'given_place' => 'Urganch shahar',
                            'address' => 'Test address',
                            'region_id' => 1,
                            'district_id' => 1,
                            'gender' => 1,
                            'citizenship_id' => 1,
                            'citizenship' => 'Uzbekistan',
                        ],
                    ],
                ],
            ]),
        ]);
    }

    public static function fakeResponse(string $url, string $jsonPath, $status = 200): void
    {
        Http::fake([
            $url => Http::response(json_decode(file_get_contents(storage_path($jsonPath)), true), $status),
        ]);
    }
}
