<?php

namespace App\Services;

use App\Clients\AtmosCardClient;
use App\DTOs\Atmos\Card\AtmosAttachCardRequestDTO;
use App\DTOs\Atmos\Card\AtmosAttachCardResponseDTO;
use App\DTOs\Atmos\Card\AtmosConfirmCardRequestDTO;
use App\DTOs\Atmos\Card\AtmosConfirmCardResponseDTO;
use App\DTOs\Atmos\CardPay\AtmosConfirmPayRequestDTO;
use App\DTOs\Atmos\CardPay\AtmosCreatePayRequestDTO;
use App\DTOs\Atmos\CardPay\AtmosCreatePayResponseDTO;
use App\DTOs\Atmos\CardPay\AtmosPreConfirmPayRequestDTO;
use App\DTOs\Atmos\CardPay\AtmosPreConfirmPayResponseDTO;
use App\DTOs\Management\BalanceFundConfirmDTO;
use App\Models\Company\BalanceFund;

class AtmosCardService
{
    public function __construct(
        private readonly AtmosCardClient $atmosCardClient
    ) {
    }

    public function attachCard(AtmosAttachCardRequestDTO $attachCardRequestDTO): AtmosAttachCardResponseDTO
    {
        return AtmosAttachCardResponseDTO::from($this->atmosCardClient->attachCard($attachCardRequestDTO));
    }

    public function confirmCard(AtmosConfirmCardRequestDTO $confirmCardRequestDTO): AtmosConfirmCardResponseDTO
    {
        return AtmosConfirmCardResponseDTO::from($this->atmosCardClient->confirmCard($confirmCardRequestDTO));
    }

    public function createPayment(AtmosCreatePayRequestDTO $createPayRequestDTO): AtmosCreatePayResponseDTO
    {
        return AtmosCreatePayResponseDTO::from($this->atmosCardClient->payCreate($createPayRequestDTO));
    }

    public function preConfirmPayment(AtmosPreConfirmPayRequestDTO $preConfirmPayRequestDTO): AtmosPreConfirmPayResponseDTO
    {
        return AtmosPreConfirmPayResponseDTO::from($this->atmosCardClient->payPreApply($preConfirmPayRequestDTO));
    }

    public function confirmPayment(AtmosConfirmPayRequestDTO $confirmPayRequestDTO): AtmosCreatePayResponseDTO
    {
        return AtmosCreatePayResponseDTO::from($this->atmosCardClient->payApplyOfd($confirmPayRequestDTO));
    }

    public function fundBalance(BalanceFundConfirmDTO $balanceFundConfirmDTO): mixed
    {
        $confirmCardResponseDTO = $this->confirmCard(AtmosConfirmCardRequestDTO::from(['transaction_id' => $balanceFundConfirmDTO->transaction_id, 'otp' => $balanceFundConfirmDTO->otp]));
        if ($confirmCardResponseDTO->result->isSucces()) {
            $createPayResponseDTO = $this->createPayment(new AtmosCreatePayRequestDTO('Fill balance', $balanceFundConfirmDTO->amount, 'User is going to fill his own balance'));
            if ($createPayResponseDTO->result->isSucces()) {
                $preConfirmPayResponseDTO = $this->preConfirmPayment(new AtmosPreConfirmPayRequestDTO($confirmCardResponseDTO->data->card_token, $createPayResponseDTO->transaction_id));
                if ($preConfirmPayResponseDTO->result->isSucces()) {
                    $confirmPayResponseDTO = $this->confirmPayment(new AtmosConfirmPayRequestDTO($createPayResponseDTO->transaction_id));
                    if ($confirmPayResponseDTO->result->isSucces()) {
                        $balanceFund = new BalanceFund;
                        $balanceFund->amount = $balanceFundConfirmDTO->amount;
                        $balanceFund->card_number = $balanceFundConfirmDTO->card_number;
                        $balanceFund->phone_number = $balanceFundConfirmDTO->phone_number;
                        $balanceFund->external_transaction_id = $confirmPayResponseDTO->store_transaction->success_trans_id;
                        $balanceFund->terminal_id = $confirmPayResponseDTO->store_transaction->terminal_id;
                        if ($balanceFund->saveOrFail()) {
                            return $balanceFund;
                        }
                    }
                }
            }
        }

        return [];
    }
}
