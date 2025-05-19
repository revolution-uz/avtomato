<?php

namespace Tests\Fakers;

use Illuminate\Support\Facades\Http;

class SmsFaker
{
    public static function fly()
    {
        Http::fake([
            'https://api.smsfly.uz/send' => Http::response([
                'success' => true,
                'reason' => 'Reason',
                'resultCode' => 200,
            ]),
        ]);
    }

    public static function sayqal()
    {
        Http::fake([
            'https://routee.sayqal.uz/sms/TransmitSMS' => Http::response([
                'transactionid' => '6988790',
                'smsid' => '101',
                'parts' => 1,
            ]),
        ]);
    }
}
