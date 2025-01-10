<?php

namespace App\Events\TelegramEvents;

use App\Enums\TelegramChannelTypeEnum;

class TelegramDebugMessageEvent extends BaseTelegramEvent
{
    public string $view = 'telegram.debug';

    public TelegramChannelTypeEnum $channelType = TelegramChannelTypeEnum::ERROR;

    public bool $isCompanyRelated = false;

    public function __construct($message, $file, $line)
    {
        $this->data = [
            'message' => $message,
            'file' => $file,
            'line' => $line,
        ];
    }
}
