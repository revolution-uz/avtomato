<?php

namespace App\Exceptions;

use Exception;

class GeneralException extends Exception
{
    public function __construct(?string $message, int $code = 0)
    {
        parent::__construct($message ?? 'Some Error', $code);
    }
}
