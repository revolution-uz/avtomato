<?php

namespace App\Events\TelegramEvents;

use App\Enums\TelegramChannelTypeEnum;

class BaseTelegramEvent
{
    public $data;
    public string $view;
    public TelegramChannelTypeEnum $channelType;
    public bool $isCompanyRelated = true;
}
