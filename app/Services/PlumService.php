<?php

namespace App\Services;

use App\Clients\PlumClient;
use App\DTOs\Scoring\Card\CreateUserCardDTO;
use App\Models\Company\Card;

class PlumService
{
    public function __construct(
        private readonly PlumClient $plumClient
    ) {
    }

    public function attachCard(Card $card)
    {
        return CreateUserCardDTO::from($this->plumClient->attachCard(
            $card->customer_id,
            $card->number,
            $card->expiry,
            $card->phone,
        ));
    }

    public function confirmCard(Card $card, $session, $otp)
    {
        $response = $this->plumClient->confirmCard($session, $otp);

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
