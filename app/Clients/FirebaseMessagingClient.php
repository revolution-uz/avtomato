<?php

namespace App\Clients;

use App\DTOs\Firebase\FirebaseDataDTO;
use App\DTOs\Firebase\FirebaseDTO;
use App\DTOs\Firebase\FirebaseNotificationDTO;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;

class FirebaseMessagingClient
{
    /**
     * @var Messaging|Application
     */
    protected mixed $messaging;

    public function __construct()
    {
        $this->messaging = app('firebase.messaging');
    }

    public function sendToClientByTopic(FirebaseDataDTO $dto, string $topic): bool
    {
        self::makeRequest(new FirebaseDTO(
            $dto,
            new FirebaseNotificationDTO(
                $dto->title,
                $dto->body
            ),
            $topic
        ));

        return true;
    }

    public function sendToClientsByTokens(FirebaseDataDTO $dto, array $tokens): bool
    {
        self::makeRequest(
            new FirebaseDTO(
                $dto,
                new FirebaseNotificationDTO(
                    $dto->title,
                    $dto->body
                )
            ),
            $tokens
        );

        return true;
    }

    private function makeRequest(FirebaseDTO $fields, array $tokens = []): void
    {
        $message = CloudMessage::fromArray($fields->toArray());
        try {
            if (empty($tokens)) {
                $response = $this->messaging->send($message);
            } else {
                $response = $this->messaging->sendMulticast($message, $tokens);
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
