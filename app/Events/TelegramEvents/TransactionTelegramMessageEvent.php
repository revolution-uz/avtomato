<?php

namespace App\Events\TelegramEvents;

use App\Models\Company\Transaction;

class TransactionTelegramMessageEvent extends BaseTelegramEvent
{
    public string $view = 'telegram.transaction';

    public function __construct(Transaction $transaction)
    {
        $this->data = [
            'record' => $transaction,
        ];
    }
}
