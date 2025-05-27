<?php

namespace App\Services;

use App\Clients\FirebaseMessagingClient;
use App\DTOs\Firebase\FirebaseDataDTO;
use App\Enums\ApplicationEnvironmentEnum;
use App\Models\Company\LoyaltyProgram\Notification;
use Illuminate\Support\Carbon;

class FirebaseService
{
    private array $locales = ['uz', 'ru'];

    public function __construct(private readonly FirebaseMessagingClient $_firebaseClient) {}

    public function sendNotification(Notification $notification): Notification
    {
        $prefix = ApplicationEnvironmentEnum::getFirebaseTopicPrefix();

        $title_ru = $notification->title_ru;
        $body_ru = $notification->body_ru;

        foreach ($this->locales as $locale) {
            $title = $notification->{'title_' . $locale} ?? $title_ru;
            $body = $notification->{'body_' . $locale} ?? $body_ru;

            $this->_firebaseClient->sendToClientByTopic(
                new FirebaseDataDTO($notification->id, $title, $body, 'notification'),
                $prefix . $locale
            );
        }

        $notification->update(['sent_at' => Carbon::now()]);

        return $notification;
    }
}
