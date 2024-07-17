<?php

namespace Tests\Fakers;

use Illuminate\Http\Client\Request;
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

    public static function getInfoByPassport(): void
    {
        Http::fake([
            'https://api.oilakredit.uz/*' => Http::response([
                'data' => [
                    'info' => [
                        'data' => [
                            'pinfl' => 31503953130027,
                            'tin' => 520212090,
                            'series' => 'AD',
                            'number' => '3119359',
                            'document' => 'AD3119359',
                            'sur_name' => 'SULTONOV',
                            'name' => 'YO‘LDOSHBOY',
                            'patronymic_name' => 'O‘RAZMETBOYEVICH',
                            'full_name' => 'SULTONOV YO‘LDOSHBOY O‘RAZMETBOYEVICH',
                            'birth_date' => '1995-03-15',
                            'birth_place' => 'URGANCH SHAHRI',
                            'living_place' => null,
                            'address' => 'Yangibozor tumani Хорезмская область, Янгибазарский район, Чўболончи ССГ, Олтинкул МСГ, ул. Огайнилар, дом 21',
                            'region_id' => 33,
                            'district_id' => 3313,
                            'status' => 1,
                            'gender' => 1,
                            'nationality_id' => 44,
                            'citizenship_id' => 182,
                            'doc_status' => 2,
                            'doc_status_name' => 'Yaroqli hujjat',
                            'given_place' => 'XORAZM VILOYATI YANGIBOZOR TUMANI IIB',
                            'given_date' => '2023-04-25',
                            'expiration_date' => '2033-04-24',
                            'citizenship' => 'O‘ZBEKISTON',
                            'nationality' => 'O‘ZBEK',
                            'is_itd' => false,
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

    public static function fakeResponseWithoutURL($jsonPath, $status, $method): void
    {
        Http::fake(function (Request $request) use ($jsonPath, $status, $method) {
            if ($method == $request->method()) {
                return Http::response(json_decode(file_get_contents(storage_path($jsonPath)), true), $status);
            } else {
                return false;
            }
        });
    }
}
