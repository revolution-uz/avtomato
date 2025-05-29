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
            'default_branch_id' => env('ATMOS_CARD_DEFAULT_BRANCH_ID', 1),
        ],
        'pinfl' => [
            'base_url' => 'https://apigw.atmos.uz',
            'username' => env('ATMOS_PINFL_USERNAME', ''),
            'password' => env('ATMOS_PINFL_PASSWORD', ''),
            'default_branch_id' => env('ATMOS_PINFL_DEFAULT_BRANCH_ID', 1),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Unired Client
    |--------------------------------------------------------------------------
    */
    'unired' => [
        'card' => [
            'base_url' => 'https://gw.cloudgate.uz/unipos',
            'login' => env('UNIRED_CARD_LOGIN', ''),
            'password' => env('UNIRED_CARD_PASSWORD', ''),
            'secret' => env('UNIRED_CARD_SECRET', ''),
            'callback-token' => env('UNIRED_CARD_CALLBACK_TOKEN', ''),
            'validity' => 365,
            'default_branch_id' => env('UNIRED_CARD_DEFAULT_BRANCH_ID', 1),

        ],
        'pinfl' => [
            'base_url' => 'https://autopaygroup.uz/api/v1/partners',
            'token' => env('UNIRED_PINFL_TOKEN', env('UNIRED_TOKEN', '')),
            'callback-token' => env('UNIRED_PINFL_CALLBACK_TOKEN', env('CALLBACK_TOKEN', '')),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sms Clients
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
        'base_url' => 'https://new-api.royxat.uz/api',
        'token' => env('ROYXAT_UZ_TOKEN', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | QR pay Client
    |--------------------------------------------------------------------------
    */
    'qr_pay' => [
        'base_url' => 'https://gw.cloudgate.uz/mts/api/v1/jsonrpc/',
        'login' => env('QR_PAY_LOGIN', ''),
        'password' => env('QR_PAY_PASSWORD', ''),
        'secret' => env('QR_PAY_SECRET', ''),
        'callback-token' => env('QR_PAY_CALLBACK_TOKEN', ''),
        'validity' => 365,
    ],

    /*
    |--------------------------------------------------------------------------
    | ICAN Client
    |--------------------------------------------------------------------------
    */
    'ican' => [
        'base_url' => 'https://api.credit.icangroup.uz',
        'auth_base_url' => 'https://api.base.icangroup.uz',
    ],

    /*
     * --------------------------------------------------------------------------
     * Loyalty program Client
     */
    'loyalty_program' => [
        'security' => [
            'qr_salt' => env('LOYALTY_PROGRAM_QR_SALT', 'salt must be hard as possible'),
        ],
    ],
];
