<?php

namespace App\Services\Scoring;

use App\Clients\RoyxatClient;
use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryInputDTO;
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
    public function scoring(RoyxatCreditHistoryInputDTO $dto): ScoringRequest
    {
        $model = new ScoringRequest;
        $model->type = ScoringRequestTypeEnum::ROYXAT_CREDIT_HISTORY;
        $model->request = $dto;
        $model->client_code = $this->royxatClient->getClientCode();
        $model->status = ScoringRequestStatusEnum::WAITING;
        $model->saveOrFail();
        $this->balanceService->chargeAmount(FeatureEnum::SCORING_UZUM_NASIYA_LIMIT, $model);
        try {
            $model->response = $this->royxatClient->getCredits($dto);
            $model->status = ScoringRequestStatusEnum::SUCCESS;
        } catch (Exception $e) {
            Log::error($e);
            $model->status = ScoringRequestStatusEnum::FAILED;
        }
        $model->saveOrFail();

        return $model;
    }
}
