<?php

namespace App\DTOs\Scoring\Base;

use Spatie\LaravelData\Data;

abstract class BaseScoringResponse extends Data
{
    public static function fromXml(string $xml)
    {
        $xmlObject = simplexml_load_string(iconv('UTF-8', 'windows-1251', $xml));

        return self::from(json_decode(json_encode($xmlObject), true));
    }
}
