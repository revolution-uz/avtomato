<?php

namespace App\Helpers;

class HideSensitiveDataFromLogging
{
    private static array $exceptions = ['api_id', 'apikey', 'login', 'psw'];

    public static function handle(array $params): array
    {
        foreach (self::$exceptions as $exception) {
            if (array_key_exists($exception, $params)) {
                $params[$exception] = self::maskString($params[$exception]);
            }
        }

        return $params;
    }

    private static function maskString($str): string
    {
        $length = strlen($str);

        if ($length < 3) {
            return '**';
        }

        if ($length <= 6) {
            return $str[0] . str_repeat('*', $length - 2) . substr($str, -1);
        }

        $masked = substr($str, 0, 2) . str_repeat('*', $length - 4) . substr($str, -2);

        return $masked;
    }
}
