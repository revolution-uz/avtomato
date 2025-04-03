<?php

namespace App\Clients;

use App\DTOs\Firebase\FirebaseDataDTO;
use App\DTOs\Firebase\FirebaseDTO;
use App\DTOs\Firebase\FirebaseNotificationDTO;
use App\Enums\Company\CompanySettingsEnum;
use App\Models\Company\CompanySettings;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseMessagingClient
{
    public function sendToClientByTopic(FirebaseDataDTO $dto, string $topic): bool
    {
        self::makeRequest(new FirebaseDTO(
            $dto,
            new FirebaseNotificationDTO($dto->title, $dto->body),
            $topic
        ));

        return true;
    }

    public function sendToClientsByTokens(FirebaseDataDTO $dto, array $tokens): bool
    {
        self::makeRequest(
            new FirebaseDTO(
                $dto,
                new FirebaseNotificationDTO($dto->title, $dto->body)
            ),
            $tokens
        );

        return true;
    }

    public function getMessaging(): Messaging
    {
        $path = CompanySettings::getConfig(CompanySettingsEnum::LOYALTY_PROGRAM_FIREBASE_PATH);

        if (! $path || ! Storage::disk('local')->exists($path)) {
            Log::channel('firebase')->error('Firebase credentials path is invalid or does not exist.');
            throw new Exception('Firebase credentials path is invalid or does not exist.');
        }

        $firebase_credentials = Storage::disk('local')->get($path);
        $firebase_credentials = json_decode($firebase_credentials, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::channel('firebase')->error('Invalid JSON in Firebase credentials.', ['error' => json_last_error_msg()]);
            throw new Exception('Invalid JSON in Firebase credentials.');
        }

        $factory = (new Factory)->withServiceAccount($firebase_credentials);

        return $factory->createMessaging();
    }

    private function makeRequest(FirebaseDTO $fields, array $tokens = []): void
    {
        $message = CloudMessage::fromArray($fields->toArray());
        $messaging = $this->getMessaging();

        try {
            if (empty($tokens)) {
                $response = $messaging->send($message);
            } else {
                $response = $messaging->sendMulticast($message, $tokens);
            }
            Log::channel('firebase')->info('messaging', [
                'request_fields' => $fields,
                'response' => $response,
                'tokens' => $tokens,
            ]);
        } catch (MessagingException|FirebaseException $e) {
            Log::channel('firebase')->error('messaging', [
                'request_fields' => $fields,
                'error' => $e,
                'tokens' => $tokens,
            ]);
        }
    }
}
