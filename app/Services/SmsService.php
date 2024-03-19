<?php

namespace App\Services;

use App\Clients\SmsFlyClient;
use App\DTOs\Sms\SendMessageDTO;
use App\DTOs\Sms\SmsFlyResponseDTO;

class SmsService
{
    public function __construct(
        private readonly SmsFlyClient $smsFlyClient
    )
    {
    }

    public function send(SendMessageDTO $messageDTO)
    {
        return SmsFlyResponseDTO::from($this->smsFlyClient->send($messageDTO));
    }

}
