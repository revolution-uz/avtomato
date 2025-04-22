<?php

namespace App\Services\Scoring;

use App\Clients\UniredClient;
use App\DTOs\Scoring\Card\CardCreateRequestDTO;
use App\DTOs\Scoring\Card\CardScoringResponseDTO;
use App\DTOs\Unired\Client\UniredClientCardRegisterRequestDTO;
use App\DTOs\Unired\Client\UniredClientCardVerifyRequestDTO;
use App\DTOs\Unired\Client\UniredClientMonitoringCardRequestDTO;
use App\DTOs\Unired\Client\UniredMonitoringCardResponseDTO;
use App\DTOs\Unired\Client\UniredRegisterCardResponseDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Enums\FeatureEnum;
use App\Models\CentralScoringCard;
use App\Models\Company\ScoringRequest;
use App\Services\User\BalanceService;
use Exception;
use Illuminate\Validation\ValidationException;

readonly class CardScoringService
{
    public function __construct(private UniredClient $uniredClient, private BalanceService $balanceService) {}

    public function scoringHumo(CardCreateRequestDTO $dto, array $dateRange, string $phone)
    {
        return $this->scoringCard($dto, $dateRange, ScoringRequestTypeEnum::HUMO, $phone);
    }

    public function scoringUzcard(CardCreateRequestDTO $dto, array $dateRange)
    {
        return $this->scoringCard($dto, $dateRange, ScoringRequestTypeEnum::UZCARD);
    }

    public function verifyHumo(CentralScoringCard $card, string $otp): true
    {
        return $this->verifyCard($card, $otp, ScoringRequestTypeEnum::HUMO);
    }

    public function verifyUzcard(CentralScoringCard $card, string $otp): true
    {
        return $this->verifyCard($card, $otp, ScoringRequestTypeEnum::UZCARD);
    }

    private function registerCard(CardCreateRequestDTO $dto, ScoringRequestTypeEnum $type, ?string $phone): UniredRegisterCardResponseDTO
    {
        $uniredDto = UniredClientCardRegisterRequestDTO::from([
            'card_number' => $dto->number,
            'expire' => $dto->expiry,
            'phone' => $phone ? "+{$phone}" : null,
            'type' => $type === ScoringRequestTypeEnum::HUMO ? 'humo' : 'uzcard',
        ]);

        try {
            $response = $this->uniredClient->cardRegister($uniredDto);

            return UniredRegisterCardResponseDTO::from($response->result);

        } catch (Exception $e) {
            throw ValidationException::withMessages(['phone' => 'Registration failed. Please, check your data again']);
        }
    }

    private function verifyCard(CentralScoringCard $card, string $otp, ScoringRequestTypeEnum $type): true
    {
        $uniredDto = UniredClientCardVerifyRequestDTO::from([
            'ext' => $card->external_id,
            'otp_code' => $otp,
            'type' => $type === ScoringRequestTypeEnum::HUMO ? 'humo' : 'uzcard',
        ]);

        try {
            $this->uniredClient->cardVerify($uniredDto);

            $card->is_verified = true;
            $card->saveOrFail();

            return true;

        } catch (Exception $e) {
            throw ValidationException::withMessages(['otp' => 'Verification failed. Please, check your data again']);
        }
    }

    private function scoringCard(CardCreateRequestDTO $dto, array $dateRange, ScoringRequestTypeEnum $type, ?string $phone = null): array
    {
        $card = CentralScoringCard::query()->whereNumber($dto->number)->first();

        if (! $card || ! $card->is_verified) {
            $response = $this->registerCard($dto, $type, $phone);

            if (! $card) {
                $card = new CentralScoringCard;
                $card->number = $dto->number;
                $card->expiry = $dto->expiry;
                $card->external_id = $response->ext;
                $card->computingBeforeInsert();
                $card->saveOrFail();
            }

            return [
                'send_otp' => true,
                'card_id' => $card->id,
                'phone_mask' => $response->phone_mask,
                'scoring_id' => null,
            ];
        }

        $uniredDto = UniredClientMonitoringCardRequestDTO::from([
            'card_number' => $dto->number,
            'date_from' => $dateRange['from'],
            'date_to' => $dateRange['to'] ?? date('Y-m-d'),
        ]);

        try {
            $response = $type === ScoringRequestTypeEnum::HUMO
                ? $this->uniredClient->monitoringHumo($uniredDto)
                : $this->uniredClient->monitoringUzcard($uniredDto);

        } catch (Exception $e) {
            throw ValidationException::withMessages(['otp' => 'Scoring failed. Please, check your data again']);
        }

        $model = new ScoringRequest;
        $model->type = $type;
        $model->request = $dto;
        $model->client_code = $this->uniredClient->getClientCode();
        $model->status = ScoringRequestStatusEnum::SUCCESS;

        $monitoringDto = UniredMonitoringCardResponseDTO::from($response->result);
        $model->response = CardScoringResponseDTO::parseFromUnired($monitoringDto);

        $model->saveOrFail();

        $this->balanceService->chargeAmount(
            $type === ScoringRequestTypeEnum::HUMO
                ? FeatureEnum::SCORING_HUMO
                : FeatureEnum::SCORING_UZCARD,
            $model
        );

        return [
            'send_otp' => false,
            'card_id' => null,
            'phone_mask' => null,
            'scoring_id' => $model->id,
        ];
    }
}
