<?php

namespace App\Services;

use App\DTOs\Atmos\AtmosResultDTO;
use App\DTOs\Atmos\Card\AtmosAttachCardResponseDTO;
use App\DTOs\Atmos\Card\AtmosConfirmCardResponseDTO;
use App\Enums\Company\CardStatusEnum;
use App\Http\Requests\Customer\CardRequest;
use App\Http\Requests\Customer\ConfirmCustomerCardRequest;
use App\Models\Company\Card;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CustomerCardService
{
    public function __construct(
        private readonly AtmosService $atmosCardService,
        private readonly ConfirmationService $confirmationService
    ) {
    }

    public function attachCard(int $customerId, CardRequest $request)
    {
        // check has already connected card
        $card = Card::query()
            ->withTrashed()
            ->where('number_hash', Hash::make($request->number))
            ->whereNotNull('external_id');

        // if connected card not exists create it and send request to card service
        if (! $card->exists()) {
            $response = $this->atmosCardService->attachCard($request->number, $request->expiry);
            if (! $response->result->isSucces()) {
                throw new BadRequestHttpException($response->result->description);
            }
            $newCard = $this->createOrGetCard($customerId, $request);

            return [$newCard, $response];
        }

        // check that the card is connected to this customer
        if ($card->where('customer_id', $customerId)->exists()) {
            throw new BadRequestHttpException('The card already connected to this customer');
        }

        $card = $this->createOrGetCard($customerId, $request);

        $this->confirmationService->send($card->phone);
        $customResponse = AtmosAttachCardResponseDTO::from(['phone' => $card->phone, 'transaction_id' => $card->id]);

        return [$card, $customResponse];
    }

    public function confirmCard(int $customerId, ConfirmCustomerCardRequest $request)
    {
        /** @var Card $card */
        $card = Card::query()
            ->where('customer_id', $customerId)
            ->findOrFail($request->card_id);

        if ($request->transaction_id === $card->id && ! empty($card->phone)) {
            $confirmed = $this->confirmationService->confirm($card->phone, $request->otp);
            if ($confirmed) {
                $card = Card::query()
                    ->withTrashed()
                    ->where('number_hash', Hash::make($card->number))
                    ->whereNotNull('external_id');
            }
            $customResponse = AtmosConfirmCardResponseDTO::from(['result' => new AtmosResultDTO(
                $confirmed ? 'OK' : 'FAILED',
                $confirmed ? __('api.success') : __('api.otp_was_wrong')
            )]);

            return [$card, $customResponse];
        }

        $atmosConfirmCardDto = $this->atmosCardService->confirmCard($request->transaction_id, $request->otp);
        if ($atmosConfirmCardDto->result->isSucces()) {
            $card->external_id = $atmosConfirmCardDto->data->card_token;
            $card->owner = $atmosConfirmCardDto->data->card_holder;
            $card->phone = '+' . $atmosConfirmCardDto->data->phone;
            $card->status = CardStatusEnum::ACTIVE;
            $card->saveOrFail();
        }

        return [$card, $atmosConfirmCardDto];
    }

    private function createOrGetCard(int $customerId, CardRequest $request)
    {
        $card = Card::query()
            ->where('customer_id', $customerId)
            ->where('number_hash', Hash::make($request->number))
            ->first();

        if ($card) {
            return $card;
        }

        $card = new Card;
        $card->fill($request->validated());
        $card->computingBeforeInsert();
        $card->customer_id = $customerId;
        $card->status = CardStatusEnum::INACTIVE;
        $card->saveOrFail();

        return $card;
    }
}
