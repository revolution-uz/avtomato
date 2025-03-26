<?php

namespace App\Events\TelegramEvents;

use App\Models\NewCustomer;

class NewCustomerTelegramMessageEvent extends BaseTelegramEvent
{
    public string $view = 'telegram.new-customer';

    public function __construct(NewCustomer $newCustomer)
    {
        $this->data = [
            'record' => $newCustomer,
        ];
    }
}
