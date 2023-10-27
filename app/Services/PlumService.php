<?php

namespace App\Services;

use App\Clients\PlumClient;
use App\Models\Company\Card;

class PlumService
{
    public function __construct(
        private readonly PlumClient $plumClient
    ) {
    }

    public function attachCard(Card $card, $phone)
    {
        return $this->plumClient->attachCard([
            'userId' => $card->customer_id,
            'cardNumber' => $card->number,
            'expireDate' => $card->expiry,
            'userPhone' => $phone,
        ]);
    }

    public function confirmCard(Card $card, $session, $otp)
    {
        $response = $this->plumClient->confirmCard([
            'session' => $session,
            'otp' => $otp,
            'isTrusted' => true,
        ]);

        $_card = $response['card'] ?? [];
        $plum_id = $_card['id'] ?? null;
        if ($plum_id) {
            $card->plum_id = $plum_id;

            return $card->saveOrFail();
        }

        return false;
    }

    public function getCards($customer_id)
    {
        return $this->plumClient->getCards([
            'userId' => $customer_id,
        ]);
    }
}
