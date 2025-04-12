<?php

namespace App\Services\LoyaltyProgram;

use App\Clients\AtmosCardClient;
use App\DTOs\Atmos\AtmosResultDTO;
use App\DTOs\Atmos\Card\AtmosAttachCardResponseDTO;
use App\DTOs\Atmos\Card\AtmosCardDTO;
use App\Enums\Company\CardStatusEnum;
use App\Enums\Company\CompanySettingsEnum;
use App\Models\Company\CompanySettings;
use App\Models\Company\LoyaltyProgram\Card;
use App\Services\AtmosService;
use App\Services\ConfirmationService;
use Exception;
use GraphQL\Error\Error;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class LoyaltyProgramUserCardService
{
    public function __construct(
        private AtmosService $atmosCardService,
        private ConfirmationService $confirmationService
    ) {}

    /**
     * @param  array{number: string, expiry: string}  $args
     * @return array{card: Card, attachData: AtmosAttachCardResponseDTO}
     *
     * @throws ValidationException|BadRequestHttpException
     */
    public function attachCard(int $userId, array $args): array
    {
        $cardQuery = Card::query()->whereNumber($args['number'])->where('expiry', $args['expiry'])->whereNotNull('external_id');

        // Handle cases where the card does not exist
        if ($cardQuery->clone()->withTrashed()->doesntExist()) {
            $response = $this->atmosCardService->attachCard($args['number'], $args['expiry']);

            // If the card exists in Atmos
            if ($response->data instanceof AtmosCardDTO) {
                $oldCard = $this->createCard($userId, $args);
                $oldCard->fillData($response->data, CardStatusEnum::INACTIVE);

                $this->confirmationService->send($oldCard->phone);

                return $this->buildResponse($oldCard);
            }

            // If the card is new but the service request failed
            if (! $response->result->isSuccess()) {
                throw new BadRequestHttpException($response->result->description);
            }

            // Create and return a new card
            $newCard = $this->createCard($userId, $args);

            return ['card' => $newCard, 'attachData' => $response];
        }

        // Handle cases for existing cards
        return $this->handleExistingCard($userId, $cardQuery);
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

        $min_balance_limit = (int) CompanySettings::getValue(CompanySettingsEnum::LOYALTY_PROGRAM_BALANCE_LIMIT);

        if ($min_balance_limit == 0) {
            $card->update(['status' => CardStatusEnum::ACTIVE]);
        } else {
            try {
                $success = $this->checkBalance($card, $min_balance_limit);
            } catch (Exception $e) {
                throw new Error($e->getMessage());
            }

            if ($success) {
                $card->update(['status' => CardStatusEnum::ACTIVE]);
            } else {
                throw ValidationException::withMessages(['card_id' => __('api.card_balance_insufficient', ['balance' => $min_balance_limit])]);
            }
        }

        return $card;
    }

    public function checkBalance(Card $card, $min_balance_limit): bool
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

        if ($card_balance < $min_balance_limit) {
            $success = false;
        }

        return $success;
    }

    /**
     * @param  Builder|Card  $cardQuery
     * @return array{card: Card, attachData: AtmosAttachCardResponseDTO}
     *
     * @throws ValidationException
     */
    private function handleExistingCard(int $userId, $cardQuery): array
    {
        // Check if the card is already active for this user
        if ($cardQuery->clone()->where('user_id', $userId)->where('status', CardStatusEnum::ACTIVE)->exists()) {
            throw ValidationException::withMessages(['number' => __('api.already_attached_card')]);
        }

        // Handle an inactive card for the user
        if ($inactiveCard = $cardQuery->clone()->where('user_id', $userId)->where('status', CardStatusEnum::INACTIVE)->first()) {
            $this->confirmationService->send($inactiveCard->phone);

            return $this->buildResponse($inactiveCard);
        }

        // Handle a deleted card for the user
        if ($deletedCard = $cardQuery->clone()->withTrashed()->where('user_id', $userId)->first()) {
            $deletedCard->restore();
            $deletedCard->update(['status' => CardStatusEnum::INACTIVE]);

            return $this->buildResponse($deletedCard);
        }

        // Card is attached to another user
        throw ValidationException::withMessages(['number' => __('api.card_in_use_by_another_user')]);
    }

    /**
     * @return array{card: Card, attachData: AtmosAttachCardResponseDTO}
     */
    private function buildResponse(Card $card): array
    {
        $response = AtmosAttachCardResponseDTO::from([
            'result' => AtmosResultDTO::from(['code' => 'OK']),
            'phone' => $card->phone,
            'transaction_id' => $card->id,
        ]);

        return ['card' => $card, 'attachData' => $response];
    }

    private function createCard(int $userId, array $args)
    {
        $card = new Card;
        $card->user_id = $userId;
        $card->number = $args['number'];
        $card->expiry = $args['expiry'];
        $card->computingBeforeInsert();
        $card->saveOrFail();

        return $card->refresh();
    }
}
