<?php

namespace App\Services\LoyaltyProgram;

use App\Clients\AtmosCardClient;
use App\DTOs\Atmos\AtmosResultDTO;
use App\DTOs\Atmos\Card\AtmosAttachCardResponseDTO;
use App\DTOs\Atmos\Card\AtmosCardDTO;
use App\Enums\Company\CardStatusEnum;
use App\Enums\Company\CompanySettingsEnum;
use App\Models\Company\CardConfirmationSms;
use App\Models\Company\CompanySettings;
use App\Models\Company\LoyaltyProgram\Card;
use App\Models\Company\LoyaltyProgram\User;
use App\Services\AtmosService;
use App\Services\ConfirmationService;
use Exception;
use GraphQL\Error\Error;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class LoyaltyProgramUserCardService
{
    const MIN_BALANCE_LIMIT = 200000;

    public function __construct(
        private AtmosService $atmosCardService,
        private ConfirmationService $confirmationService
    ) {}

    public function attachCard(int $userId, array $args): array
    {
        // check has already connected card
        $cardQuery = Card::query()->whereNumber($args['number'])->where('expiry', $args['expiry'])->whereNotNull('external_id');

        // if connected card not exists create it and send request to card service
        if ($cardQuery->clone()->withTrashed()->doesntExist()) {
            $response = $this->atmosCardService->attachCard($args['number'], $args['expiry']);

            if ($response->data instanceof AtmosCardDTO) {
                $oldCard = $this->createOrGetCard($userId, $args);
                $oldCard->fillData($response->data, CardStatusEnum::INACTIVE);

                $this->confirmationService->send($oldCard->phone);
                $customResponse = AtmosAttachCardResponseDTO::from(['result' => AtmosResultDTO::from(['code' => 'OK']), 'phone' => $oldCard->phone, 'transaction_id' => $oldCard->id]);

                return [$oldCard, $customResponse];
            } else {
                if (! $response->result->isSuccess()) {
                    throw new BadRequestHttpException($response->result->description);
                }
                $newCard = $this->createOrGetCard($userId, $args);

                return [$newCard, $response];
            }
        }

        // check that the card is connected to this user
        if ($cardQuery->clone()->where('user_id', $userId)->where('status', CardStatusEnum::ACTIVE)->exists()) {
            throw ValidationException::withMessages(['number' => __('api.already_attached_card')]);
        } elseif ($inactiveCard = $cardQuery->clone()->where('user_id', $userId)->where('status', CardStatusEnum::INACTIVE)->first()) {
            if (CardConfirmationSms::query()->where('phone', $inactiveCard->phone)->where('created_at', '>', now()->subMinutes(2))->doesntExist()) {
                $this->confirmationService->send($inactiveCard->phone);
            }
            $customResponse = AtmosAttachCardResponseDTO::from(['result' => AtmosResultDTO::from(['code' => 'OK']), 'phone' => $inactiveCard->phone, 'transaction_id' => $inactiveCard->id]);

            return [$inactiveCard, $customResponse];
        } elseif ($deletedCard = $cardQuery->clone()->withTrashed()->where('user_id', $userId)->first()) {
            $deletedCard->restore();
            $deletedCard->update(['status' => CardStatusEnum::INACTIVE]);

            $customResponse = AtmosAttachCardResponseDTO::from(['result' => AtmosResultDTO::from(['code' => 'OK']), 'phone' => $deletedCard->phone, 'transaction_id' => $deletedCard->id]);

            return [$deletedCard, $customResponse];
        }

        $newCard = new Card;
        $newCard->fill($cardQuery->first()->makeVisible(['external_id'])->toArray());
        $newCard->user_id = $userId;
        $newCard->computingBeforeInsert();
        $newCard->saveOrFail();

        $this->confirmationService->send($newCard->phone);
        $customResponse = AtmosAttachCardResponseDTO::from(['result' => AtmosResultDTO::from(['code' => 'OK']), 'phone' => $newCard->phone, 'transaction_id' => $newCard->id]);

        return [$newCard, $customResponse];
    }

    public function confirmCard(int $userId, array $args): Card
    {
        $card = Card::query()->where('user_id', $userId)->findOrFail($args['card_id']);

        if ($args['transaction_id'] == $card->id && ! empty($card->phone)) {
            $confirmed = $this->confirmationService->confirm($card->phone, $args['otp']);

            if (! $confirmed) {
                throw ValidationException::withMessages(['otp' => __('api.otp_was_wrong')]);
            }
        } else {
            $responseDto = $this->atmosCardService->confirmCard($args['transaction_id'], $args['otp']);

            if ($responseDto->result->isSuccess()) {
                $card->fillData($responseDto->data, CardStatusEnum::INACTIVE);
            } else {
                throw ValidationException::withMessages(['otp' => $responseDto->result->description]);
            }
        }

        if (CompanySettings::getValue(CompanySettingsEnum::LOYALTY_PROGRAM_BALANCE_CHECK) == 0) {
            $card->update(['status' => CardStatusEnum::ACTIVE]);
        } else {
            try {
                $success = $this->checkBalance($card);
            } catch (Exception $e) {
                throw new Error($e->getMessage());
            }

            if ($success) {
                $card->update(['status' => CardStatusEnum::ACTIVE]);
            } else {
                throw ValidationException::withMessages(['card_id' => __('api.card_balance_insufficient', ['balance' => self::MIN_BALANCE_LIMIT])]);
            }
        }

        return $card;
    }

    public function checkBalance(Card $card): bool
    {
        $success = true;

        try {
            $atmosCardInfoResponse = (new AtmosCardClient)->getCardInfo($card->external_id);

            if (! $atmosCardInfoResponse->result->isSuccess()) {
                throw new Exception;
            }

        } catch (Exception $e) {
            throw new Exception('Unable to retrieve card balance');
        }

        $card_balance = $atmosCardInfoResponse->card->balance_sum / 100;

        if ($card_balance < self::MIN_BALANCE_LIMIT) {
            $success = false;
        }

        return $success;
    }

    private function createOrGetCard(int $userId, array $args)
    {
        if (
            $card = Card::query()->where('user_id', $userId)->whereNumber($args['number'])->where('expiry', $args['expiry'])->first()
        ) {
            return $card;
        }

        $card = new Card;
        $card->user_id = $userId;
        $card->number = $args['number'];
        $card->expiry = $args['expiry'];
        $card->computingBeforeInsert();
        $card->saveOrFail();

        return $card->refresh();
    }
}
