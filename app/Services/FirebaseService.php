<?php

namespace App\Services;

use App\Clients\FirebaseMessagingClient;
use App\DTOs\Firebase\FirebaseDataDTO;
use App\Enums\ApplicationEnvironmentEnum;
use App\Models\Company\LoyaltyProgram\Notification;
use Illuminate\Support\Collection;

class FirebaseService
{
    private array $locales = ['uz', 'ru'];

    public function __construct(private readonly FirebaseMessagingClient $_firebaseClient) {}

    /**
     * @param  Notification[]|Collection<int, Notification>  $notifications
     */
    public function sendNotification($notifications): true
    {
        $prefix = ApplicationEnvironmentEnum::getFirebaseTopicPrefix();

        foreach ($notifications as $notification) {
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

            $notification->update(['is_sent' => true]);
        }

        return true;
    }
}
