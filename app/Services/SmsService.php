<?php

namespace App\Services;

use App\Clients\SmsFlyClient;
use App\DTOs\Sms\SendMessageDTO;

class SmsService
{
    public function __construct(
        private readonly SmsFlyClient $smsFlyClient
    )
    {
    }

    public function send(SendMessageDTO $messageDTO)
    {
        return $this->smsFlyClient->send($messageDTO);
    }
}
