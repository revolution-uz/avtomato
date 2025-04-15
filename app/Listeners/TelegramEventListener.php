<?php

namespace App\Listeners;

use App\Enums\Main\SettingsEnum;
use App\Events\TelegramEvents\BaseTelegramEvent;
use App\Jobs\SendMessageToTelegramJob;
use App\Models\Settings;
use Exception;
use Throwable;

class TelegramEventListener
{
    public string $connection = 'redis';

    public string $queue = 'telegram';

    /**
     * @throws Exception|Throwable
     */
    public function handle(BaseTelegramEvent $event): void
    {
        if (
            ! Settings::getValue(SettingsEnum::TELEGRAM_BOT_TOKEN)
            || ! Settings::getValue(SettingsEnum::TELEGRAM_CHAT_ID)
        ) {
            return;
        }

        $data = [
            'chat_id' => Settings::getValue(SettingsEnum::TELEGRAM_CHAT_ID),
            'text' => view($event->view, $event->data)->render(),
            'parse_mode' => 'html',
        ];

        SendMessageToTelegramJob::dispatch($data, $event);
    }
}
