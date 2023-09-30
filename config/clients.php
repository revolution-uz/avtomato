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
];
