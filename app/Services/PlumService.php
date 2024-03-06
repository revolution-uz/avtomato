<?php

namespace App\Services;

use App\Clients\PlumClient;
use App\DTOs\Management\ConfirmPaymentFundBalanceResponseDTO;
use App\DTOs\Management\FundBalanceResponseDTO;
use App\DTOs\Scoring\Card\CreateUserCardDTO;
use App\Models\Company\Card;
use Illuminate\Support\Facades\Crypt;

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
            Crypt::decryptString($card->number),
            $card->expiry,
            $card->phone,
        ));
    }

    public function confirmCard(Card $card, $session, $otp)
    {
        $response = $this->plumClient->confirmCard($session, $otp);

        $_card = $response['card'] ?? [];
        $plum_id = $_card['id'] ?? null;
        $owner = $_card['owner'] ?? null;
        if ($plum_id) {
            $card->plum_id = $plum_id;
            $card->owner = $owner;

            return $card->saveOrFail();
        }

        return false;
    }

    public function fundBalance($data): FundBalanceResponseDTO
    {
        return FundBalanceResponseDTO::from($this->plumClient->payment($data));
    }

    public function confirmPayment($data): ConfirmPaymentFundBalanceResponseDTO
    {
        return ConfirmPaymentFundBalanceResponseDTO::from($this->plumClient->confirmPayment($data));
    }

    public function getCards($customer_id)
    {
        return $this->plumClient->getCards([
            'userId' => $customer_id,
        ]);
    }
}
