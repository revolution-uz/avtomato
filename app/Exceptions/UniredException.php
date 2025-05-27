<?php

namespace App\Exceptions;

use Exception;

class UniredException extends Exception
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message);
    }
}
