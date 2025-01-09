<?php

namespace App\Events\TelegramEvents;

use App\Enums\TelegramChannelTypeEnum;
use App\Models\Company\Transaction;

class TransactionTelegramMessageEvent extends BaseTelegramEvent
{
    public string $view = 'telegram.transaction';

    public TelegramChannelTypeEnum $channelType = TelegramChannelTypeEnum::COMPANY_GROUP;

    public function __construct(Transaction $transaction)
    {
        $this->data = [
            'record' => $transaction,
        ];
    }
}
