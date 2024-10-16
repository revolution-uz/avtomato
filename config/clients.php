<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Asaxiy Client
    |--------------------------------------------------------------------------
    */
    'asaxiy' => [
        'base_url' => 'https://api.abrand.uz/partner',
        'token' => env('ASAXIY_TOKEN', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Real-soft Client
    |--------------------------------------------------------------------------
    */
    'online_mahalla' => [
        'base_url' => 'https://api.online-mahalla.uz',
    ],
    'oila_kredit' => [
        'base_url' => 'https://api.oilakredit.uz',
    ],

    /*
    |--------------------------------------------------------------------------
    | Plum Client
    |--------------------------------------------------------------------------
    */
    'plum' => [
        'base_url' => 'https://pay.myuzcard.uz/api',
        'token' => env('PLUM_TOKEN', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Atmos Client
    |--------------------------------------------------------------------------
    */
    'atmos' => [
        'card' => [
            'base_url' => 'https://partner.atmos.uz',
            'store_id' => env('ATMOS_CARD_STORE_ID'),
            'username' => env('ATMOS_CARD_USERNAME', ''),
            'password' => env('ATMOS_CARD_PASSWORD', ''),
        ],
        'pinfl' => [
            'base_url' => 'https://apigw.atmos.uz',
            'username' => env('ATMOS_PINFL_USERNAME', ''),
            'password' => env('ATMOS_PINFL_PASSWORD', ''),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Unired Client
    |--------------------------------------------------------------------------
    */
    'unired' => [
        'base_url' => 'https://autopay-tezpay.unired.uz/api/partners',
        'token' => env('UNIRED_TOKEN', ''),
        'callback-token' => env('CALLBACK_TOKEN', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sms Fly Client
    |--------------------------------------------------------------------------
    */
    'sms_fly' => [
        'base_url' => 'https://api.smsfly.uz',
    ],
    'sms_sayqal' => [
        'base_url' => 'https://routee.sayqal.uz',
    ],

    /*
    |--------------------------------------------------------------------------
    | Royxat Client
    |--------------------------------------------------------------------------
    */
    'royxat' => [
        'base_url' => 'https://service.royxat.uz/api/credits',
        'token' => env('ROYXAT_UZ_TOKEN', ''),
    ],
];
