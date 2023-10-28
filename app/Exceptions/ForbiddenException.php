<?php

namespace App\Exceptions;

use Exception;

class ForbiddenException extends Exception
{
    public function __construct(string $message = 'Forbidden')
    {
        parent::__construct($message);
    }
}
