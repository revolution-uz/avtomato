<?php

namespace App\Listeners;

use App\Enums\Company\CompanySettingsEnum;
use App\Events\TelegramEvents\BaseTelegramEvent;
use App\Jobs\SendMessageToTelegramJob;
use App\Models\Company\CompanySettings;
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
            CompanySettings::getConfig(CompanySettingsEnum::TELEGRAM_BOT_ENABLED) !== '1'
            or empty($chatId = CompanySettings::getConfig(CompanySettingsEnum::TELEGRAM_CHAT_ID))
            or empty(CompanySettings::getConfig(CompanySettingsEnum::TELEGRAM_BOT_TOKEN))
        ) {
            return;
        }

        $data = [
            'chat_id' => $chatId,
            'text' => view($event->view, $event->data)->render(),
            'parse_mode' => 'html',
        ];

        SendMessageToTelegramJob::dispatch($data, $event);
    }
}
