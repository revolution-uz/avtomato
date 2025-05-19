<?php

namespace App\Listeners;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\FeatureEnum;
use App\Events\TelegramEvents\BaseTelegramEvent;
use App\Jobs\CompanySendMessageToTelegramJob;
use App\Models\Company\CompanySettings;
use App\Models\Company\Feature;
use Exception;
use Throwable;

class CompanyTelegramEventListener
{
    public string $connection = 'redis';

    public string $queue = 'telegram';

    /**
     * @throws Exception|Throwable
     */
    public function handle(BaseTelegramEvent $event): void
    {
        if (
            ! Feature::isFeatureActive(FeatureEnum::TELEGRAM_BOT_FEATURE)
            or CompanySettings::getValue(CompanySettingsEnum::TELEGRAM_BOT_ENABLED) !== '1'
            or empty($chatId = CompanySettings::getValue(CompanySettingsEnum::TELEGRAM_CHAT_ID))
            or empty(CompanySettings::getValue(CompanySettingsEnum::TELEGRAM_BOT_TOKEN))
        ) {
            return;
        }

        $data = [
            'chat_id' => $chatId,
            'text' => view($event->view, $event->data)->render(),
            'parse_mode' => 'html',
        ];

        CompanySendMessageToTelegramJob::dispatch($data, $event);
    }
}
