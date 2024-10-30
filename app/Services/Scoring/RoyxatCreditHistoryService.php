<?php

namespace App\Services\Scoring;

use App\Clients\RoyxatClient;
use App\DTOs\Scoring\UzumNasiya\UzumNasiyaLimitCreateRequestDTO;
use App\DTOs\Scoring\UzumNasiya\UzumNasiyaLimitResponseDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\ScoringRequest;
use App\Services\User\BalanceService;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

readonly class RoyxatCreditHistoryService
{
    public function __construct(
        private RoyxatClient $royxatClient,
        private BalanceService $balanceService
    ) {}

    /**
     * @throws Throwable
     */
    public function scoring(UzumNasiyaLimitCreateRequestDTO $dto): ScoringRequest
    {
        $model = new ScoringRequest;
        $model->type = ScoringRequestTypeEnum::UZUM_NASIYA_LIMIT;
        $model->request = $dto;
        $model->client_code = $this->royxatClient->getClientCode();
        $model->status = ScoringRequestStatusEnum::WAITING;
        $model->saveOrFail();
        $this->balanceService->chargeAmount(FeatureEnum::SCORING_UZUM_NASIYA_LIMIT, $model);
        try {
            $model->response = UzumNasiyaLimitResponseDTO::from($this->royxatClient->scoringUzumNasiya($dto->toArray()));
            $model->status = ScoringRequestStatusEnum::SUCCESS;
        } catch (Exception $e) {
            Log::error($e);
            $model->status = ScoringRequestStatusEnum::FAILED;
        }
        $model->saveOrFail();

        return $model;
    }
}
