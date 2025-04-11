<?php

namespace App\Services;

use App\DTOs\Atmos\AtmosResultDTO;
use App\DTOs\Atmos\Card\AtmosAttachCardResponseDTO;
use App\DTOs\Atmos\Card\AtmosCardDTO;
use App\DTOs\Atmos\Card\AtmosConfirmCardResponseDTO;
use App\Enums\Company\CardStatusEnum;
use App\Enums\Company\SmsTemplateEnum;
use App\Models\Company\Card;
use App\Models\Company\CardConfirmationSms;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class CustomerCardService
{
    public function __construct(
        private AtmosService $atmosCardService,
        private ConfirmationService $confirmationService
    ) {}

    public function attachCard(int $customerId, array $request): array
    {
        // check has already connected card
        $cardQuery = Card::query()->whereNumber($request['number'])->where('expiry', $request['expiry'])->whereNotNull('external_id');

        // if connected card not exists create it and send request to card service
        if ((clone $cardQuery)->doesntExist()) {
            $response = $this->atmosCardService->attachCard($request['number'], $request['expiry']);
            if ($response->data instanceof AtmosCardDTO) {
                $oldCard = $this->createOrGetCard($customerId, $request);
                $oldCard->fillData($response->data, CardStatusEnum::INACTIVE);

                $this->confirmationService->send($oldCard->phone, SmsTemplateEnum::CARD_CONFIRMATION);
                $customResponse = AtmosAttachCardResponseDTO::from(['result' => AtmosResultDTO::from(['code' => 'OK']), 'phone' => $oldCard->phone, 'transaction_id' => $oldCard->id]);

                return [$oldCard, $customResponse];
            } else {
                if (! $response->result->isSuccess()) {
                    throw new BadRequestHttpException($response->result->description);
                }
                $newCard = $this->createOrGetCard($customerId, $request);

                return [$newCard, $response];
            }
        }

        // check that the card is connected to this customer
        if ((clone $cardQuery)->where('customer_id', $customerId)->where('status', CardStatusEnum::ACTIVE)->exists()) {
            throw ValidationException::withMessages(['number' => __('api.already_attached_card')]);
        } elseif ($inactiveCard = (clone $cardQuery)->where('customer_id', $customerId)->where('status', CardStatusEnum::INACTIVE)->first()) {
            if (CardConfirmationSms::query()->where('phone', $inactiveCard->phone)->where('created_at', '>', now()->subMinutes(2))->doesntExist()) {
                $this->confirmationService->send($inactiveCard->phone, SmsTemplateEnum::CARD_CONFIRMATION);
            }
            $customResponse = AtmosAttachCardResponseDTO::from(['result' => AtmosResultDTO::from(['code' => 'OK']), 'phone' => $inactiveCard->phone, 'transaction_id' => $inactiveCard->id]);

            return [$inactiveCard, $customResponse];
        }

        $newCard = new Card;
        $newCard->fill($cardQuery->first()->toArray());
        $newCard->customer_id = $customerId;
        $newCard->computingBeforeInsert();
        $newCard->saveOrFail();

        $this->confirmationService->send($newCard->phone, SmsTemplateEnum::CARD_CONFIRMATION);
        $customResponse = AtmosAttachCardResponseDTO::from(['result' => AtmosResultDTO::from(['code' => 'OK']), 'phone' => $newCard->phone, 'transaction_id' => $newCard->id]);

        return [$newCard, $customResponse];
    }

    public function confirmCard(int $customerId, array $request): array
    {
        /** @var Card $card */
        $card = Card::query()
            ->where('customer_id', $customerId)
            ->findOrFail($request['card_id']);

        if ($request['transaction_id'] == $card->id && ! empty($card->phone)) {
            $confirmed = $this->confirmationService->confirm($card->phone, $request['otp']);
            if ($confirmed) {
                $card->update(['status' => CardStatusEnum::ACTIVE]);
            }
            $customResponse = AtmosConfirmCardResponseDTO::from(['result' => new AtmosResultDTO(
                $confirmed ? 'OK' : 'FAILED',
                $confirmed ? __('api.success') : __('api.otp_was_wrong')
            )]);

            return [$card, $customResponse];
        }

        $atmosConfirmCardDto = $this->atmosCardService->confirmCard($request['transaction_id'], $request['otp']);
        if ($atmosConfirmCardDto->result->isSuccess()) {
            $card->fillData($atmosConfirmCardDto->data, CardStatusEnum::ACTIVE);
        }

        return [$card, $atmosConfirmCardDto];
    }

    private function createOrGetCard(int $customerId, array $request)
    {
        $card = Card::query()->where('customer_id', $customerId)->whereNumber($request['number'])
            ->where('expiry', $request['expiry'])->first();

        if ($card) {
            return $card;
        }

        $card = new Card;
        $card->fill($request);
        $card->computingBeforeInsert();
        $card->customer_id = $customerId;
        $card->status = CardStatusEnum::INACTIVE;
        $card->saveOrFail();

        return $card;
    }
}
