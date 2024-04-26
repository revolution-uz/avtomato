<?php

namespace App\Services;

use App\Clients\AtmosCardClient;
use App\DTOs\Management\ConfirmPaymentFundBalanceResponseDTO;
use App\DTOs\Management\FundBalanceResponseDTO;
use App\DTOs\Scoring\Card\Atmos\AtmosConfirmUserCardDTO;
use App\DTOs\Scoring\Card\Atmos\AtmosCreateUserCardDTO;
use App\Models\Company\Card;
use Illuminate\Support\Facades\Crypt;

class AtmosCardService
{
    public function __construct(
        private readonly AtmosCardClient $atmosCardClient
    )
    {
    }

    public function attachCard(Card $card)
    {
        return AtmosCreateUserCardDTO::from($this->atmosCardClient->attachCard(
            Crypt::decryptString($card->number),
            $card->expiry,
        ));
    }

    public function confirmCard(Card $card, $transaction_id, $otp)
    {
        $confirmedCard = AtmosConfirmUserCardDTO::from($this->atmosCardClient->confirmCard($transaction_id, $otp));
        if ($confirmedCard->result->isSucces()) {
            $card->external_id = $confirmedCard->data->card_token;
            $card->owner = $confirmedCard->data->card_holder;

            return $card->saveOrFail();
        }

        return false;
    }
}
