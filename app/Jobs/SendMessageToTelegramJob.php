<?php

namespace App\Jobs;

use App\Enums\Main\SettingsEnum;
use App\Events\TelegramEvents\BaseTelegramEvent;
use App\Models\Settings;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendMessageToTelegramJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The message data to be sent.
     */
    public mixed $data;

    private BaseTelegramEvent $event;

    /**
     * Create a new job instance.
     *
     * @param  mixed  $data  The message data to be sent.
     * @param  BaseTelegramEvent  $event  The event triggering the message sending.
     */
    public function __construct(mixed $data, BaseTelegramEvent $event)
    {
        $this->data = $data;
        $this->event = $event;
        $this->onQueue('default');
    }

    /**
     * Execute the job.
     *
     * Sends the message to the Telegram API.
     *
     * @throws Exception If an error occurs while sending the message.
     */
    public function handle(): void
    {
        $botToken = Settings::getValue(SettingsEnum::TELEGRAM_BOT_TOKEN);

        $response = Http::post('https://api.telegram.org/bot' . $botToken . '/sendMessage', $this->data);

        if ($response->status() !== 200) {
            Log::channel('telegram')->error(get_class($this->event), [
                'url' => 'https://api.telegram.org/bot' . $botToken . '/sendMessage',
                'data' => $this->data,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);
            throw new Exception('Telegram error', $response->status());
        }
    }

    /**
     * Get the unique ID for the job.
     */
    public function uniqueId(): string
    {
        return 'central-telegram-message-' . md5(serialize($this->data));
    }
}
