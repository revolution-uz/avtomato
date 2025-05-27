<?php

namespace App\Exceptions;

use Exception;

class QrPayException extends Exception
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message);
    }
}
