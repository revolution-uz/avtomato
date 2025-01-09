<?php

namespace App\Listeners;

use App\Enums\Main\SettingsEnum;
use App\Events\TelegramEvents\BaseTelegramEvent;
use App\Jobs\SendMessageToTelegramJob;
use App\Models\Settings;
use App\Models\TelegramChannel;
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
            Settings::getConfig(SettingsEnum::ALLOW_TELEGRAM_NOTIFICATION) !== '1'
            or ! $chat = TelegramChannel::query()->where('type', $event->channelType)
                ->when($event->isCompanyRelated, fn ($q) => $q
                    ->where('company_id', tenant('id')))
                ->first()
        ) {
            return;
        }

        $data = [
            'chat_id' => $chat->chat_id,
            'text' => view($event->view, $event->data)->render(),
            'parse_mode' => 'html',
        ];

        SendMessageToTelegramJob::dispatch($data, $event);
    }
}
