<?php

namespace App\Services\Scoring;

use App\Clients\UniredClient;
use App\DTOs\Scoring\Card\CardCreateRequestDTO;
use App\DTOs\Scoring\Card\CardScoringResponseDTO;
use App\DTOs\Unired\Client\UniredClientCardRegisterRequestDTO;
use App\DTOs\Unired\Client\UniredClientCardVerifyRequestDTO;
use App\DTOs\Unired\Client\UniredClientMonitoringCardRequestDTO;
use App\DTOs\Unired\Client\UniredMonitoringHumoResponseDTO;
use App\DTOs\Unired\Client\UniredMonitoringUzcardResponseDTO;
use App\DTOs\Unired\Client\UniredRegisterCardResponseDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Enums\FeatureEnum;
use App\Models\CentralScoringCard;
use App\Models\Company\ScoringRequest;
use App\Services\User\BalanceService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

readonly class CardScoringService
{
    const CARD_TYPE_HUMO = 'humo';
    const CARD_TYPE_UZCARD = 'uzcard';
    const CONFIRMED_CARD_ERROR_CODE = 'card_already_confirmed';

    public function __construct(private UniredClient $uniredClient, private BalanceService $balanceService) {}

    public function scoringHumo(CardCreateRequestDTO $dto, string $phone)
    {
        return $this->scoringCard($dto, ScoringRequestTypeEnum::HUMO, $phone);
    }

    public function scoringUzcard(CardCreateRequestDTO $dto)
    {
        return $this->scoringCard($dto, ScoringRequestTypeEnum::UZCARD);
    }

    public function verifyHumo(CentralScoringCard $card, string $otp): true
    {
        return $this->verifyCard($card, $otp, ScoringRequestTypeEnum::HUMO);
    }

    public function verifyUzcard(CentralScoringCard $card, string $otp): true
    {
        return $this->verifyCard($card, $otp, ScoringRequestTypeEnum::UZCARD);
    }

    private function scoringCard(CardCreateRequestDTO $dto, ScoringRequestTypeEnum $type, ?string $phone = null): array
    {
        $card = CentralScoringCard::query()->whereNumber($dto->number)->first();

        if (! $card || ! $card->is_verified) {
            $response = $this->registerCard($dto, $type, $phone);

            if (! $card && $response instanceof UniredRegisterCardResponseDTO) {
                $card = $this->initializeCard($dto, $response);
            } elseif ($response instanceof CentralScoringCard) {
                $card = $response;
            }

            if (! $card->is_verified) {
                return [
                    'send_otp' => true,
                    'card_id' => $card->id,
                    'phone_mask' => $response->phone_mask,
                    'scoring_id' => null,
                ];
            }
        }

        $uniredDto = UniredClientMonitoringCardRequestDTO::from([
            'card_number' => $dto->number,
            'date_from' => $type === ScoringRequestTypeEnum::HUMO ?
                Carbon::now()->subMonths(3)->addDay()->toDateString() :
                Carbon::now()->subYear()->toDateString(),
        ]);

        try {
            $response = $type === ScoringRequestTypeEnum::HUMO
                ? $this->uniredClient->monitoringHumo($uniredDto)
                : $this->uniredClient->monitoringUzcard($uniredDto);

            $model = new ScoringRequest;
            $model->type = $type;
            $model->request = $dto;
            $model->client_code = $this->uniredClient->getClientCode();
            $model->status = ScoringRequestStatusEnum::SUCCESS;
            if ($type === ScoringRequestTypeEnum::HUMO) {
                $model->response = CardScoringResponseDTO::parseFromUniredHumo(
                    UniredMonitoringHumoResponseDTO::from($response->result)
                );
            } else {
                $totalPages = 1;
                for ($i = 0; $i < $totalPages; $i++) {
                    $uniredDto->page_number = $i;
                    $response = $this->uniredClient->monitoringUzcard($uniredDto);

                    $result = UniredMonitoringUzcardResponseDTO::from($response->result);
                    $responseDto = CardScoringResponseDTO::parseFromUniredUzcard($result, $responseDto ?? null);

                    $totalPages = $result->total_pages;
                }

                $model->response = $responseDto;
            }
            $model->saveOrFail();

            $this->balanceService->chargeAmount(
                $type === ScoringRequestTypeEnum::HUMO ? FeatureEnum::SCORING_HUMO : FeatureEnum::SCORING_UZCARD,
                $model
            );

            return [
                'send_otp' => false,
                'card_id' => null,
                'phone_mask' => null,
                'scoring_id' => $model->id,
            ];

        } catch (Exception $e) {
            throw ValidationException::withMessages(['otp' => __('api.' . $this->parseError($e->getMessage()))]);
        }
    }

    private function verifyCard(CentralScoringCard $card, string $otp, ScoringRequestTypeEnum $type): true
    {
        $uniredDto = UniredClientCardVerifyRequestDTO::from([
            'ext' => $card->external_id,
            'otp_code' => $otp,
            'type' => $type === ScoringRequestTypeEnum::HUMO ? self::CARD_TYPE_HUMO : self::CARD_TYPE_UZCARD,
        ]);

        try {
            $this->uniredClient->cardVerify($uniredDto);

            $card->is_verified = true;
            $card->saveOrFail();

            return true;

        } catch (Exception $e) {
            throw ValidationException::withMessages(['otp' => __('api.' . $this->parseError($e->getMessage()))]);
        }
    }

    private function registerCard(CardCreateRequestDTO $dto, ScoringRequestTypeEnum $type, ?string $phone): UniredRegisterCardResponseDTO|CentralScoringCard
    {
        $uniredDto = UniredClientCardRegisterRequestDTO::from([
            'card_number' => $dto->number,
            'expire' => $dto->expiry,
            'phone' => $phone ? "+{$phone}" : null,
            'type' => $type === ScoringRequestTypeEnum::HUMO ? self::CARD_TYPE_HUMO : self::CARD_TYPE_UZCARD,
        ]);

        try {
            $response = $this->uniredClient->cardRegister($uniredDto);

            return UniredRegisterCardResponseDTO::from($response->result);

        } catch (Exception $e) {
            $code = $this->parseError($e->getMessage());

            if ($code === self::CONFIRMED_CARD_ERROR_CODE) {
                return $this->initializeCard($dto, null, true);
            }

            throw ValidationException::withMessages(['phone' => __('api.' . $code)]);
        }
    }

    private function parseError(string $message): string
    {
        $message = $this->sanitizeErrorMessage($message);

        switch ($message) {
            case 'Ошибка: card_number уже подтвержден!':
                return self::CONFIRMED_CARD_ERROR_CODE;
            case 'Неверный OTP код!':
                return 'invalid_otp';
            case 'OTP код истек!':
                return 'otp_expired';
            default:
                return 'unknown_error';
        }
    }

    private function sanitizeErrorMessage(string $message): string
    {
        return preg_replace('/\b\d{16}\b/', 'card_number', $message);
    }

    private function initializeCard(CardCreateRequestDTO $dto, $response = null, bool $isVerified = false): CentralScoringCard
    {
        $card = new CentralScoringCard;
        $card->number = $dto->number;
        $card->expiry = $dto->expiry;
        $card->external_id = $response?->ext;
        $card->computingBeforeInsert();
        $card->is_verified = $isVerified;
        $card->saveOrFail();

        return $card;
    }
}
