<?php

namespace App\Services\Scoring;

use App\Clients\AsaxiyClient;
use App\DTOs\Scoring\Katm\CreditHistory\KatmCreditHistoryResponseDTO;
use App\DTOs\Scoring\Katm\InpsHistory\KatmInpsHistoryResponseDTO;
use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use App\DTOs\Scoring\Katm\PensionInfo\KatmPensionInfoResponseDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Enums\External\AsaxiyRequestStatusEnum;
use App\Exceptions\BalanceIsNotEnoughException;
use App\Models\Company\ScoringRequest;
use App\Services\User\BalanceService;
use InvalidArgumentException;
use Throwable;

readonly class KatmService
{
    public function __construct(
        private AsaxiyClient $asaxiyClient,
        private BalanceService $balanceService,
    ) {}

    /**
     * @throws Throwable
     * @throws BalanceIsNotEnoughException
     */
    public function createRequest(KatmCreateRequestDTO $dto, ScoringRequestTypeEnum $type): ScoringRequest
    {
        $model = new ScoringRequest;
        $model->type = $type;
        $model->request = $dto;
        $model->client_code = $this->asaxiyClient->getClientCode();
        $model->status = ScoringRequestStatusEnum::CREATED;
        $model->saveOrFail();
        $this->balanceService->chargeAmount($type->getFeatureEnum(), $model);

        $externalId = $this->asaxiyClient->createRequest(
            array_merge(
                $dto->convertToKatmRequest(),
                ['type' => $type->convertToAsaxiyFormat()],
            )
        );

        if ($externalId) {
            $model->ext_id = $externalId;
            $model->status = ScoringRequestStatusEnum::WAITING;
        } else {
            $model->status = ScoringRequestStatusEnum::FAILED;
        }

        $model->saveOrFail();

        return $model;
    }

    public function syncRemoteStatus(ScoringRequest $scoringRequest): void
    {
        $response = $this->asaxiyClient->getRequestStatus($scoringRequest->ext_id);

        if ($response) {
            if ($response['status_id'] === AsaxiyRequestStatusEnum::SUCCESS->value) {
                $scoringRequest->status = ScoringRequestStatusEnum::SUCCESS;
                $scoringRequest->response = match ($scoringRequest->type) {
                    ScoringRequestTypeEnum::KATM_CREDIT_HISTORY => KatmCreditHistoryResponseDTO::parseFromXml($response['data']),
                    ScoringRequestTypeEnum::KATM_INPS_HISTORY => KatmInpsHistoryResponseDTO::parseFromXml($response['data']),
                    ScoringRequestTypeEnum::KATM_PENSION_INFO => KatmPensionInfoResponseDTO::parseFromXml($response['data']),
                    default => throw new InvalidArgumentException,
                };
                $scoringRequest->saveOrFail();
            }
        } else {
            $scoringRequest->status = ScoringRequestStatusEnum::FAILED;
            $scoringRequest->saveOrFail();
        }
    }
}
