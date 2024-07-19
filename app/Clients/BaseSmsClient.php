<?php

namespace App\Clients;

use App\Models\Company\SmsMessage;

abstract class BaseSmsClient extends BaseClient
{
    abstract public function sendMessage(int $id, int $phone, string $text): mixed;

    abstract public function getStatus(SmsMessage $message): mixed;
}
