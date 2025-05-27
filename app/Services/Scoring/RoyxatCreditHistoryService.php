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

readonly class RoyxatCreditHistoryService
{
    public function __construct(
        private RoyxatClient $royxatClient,
        private BalanceService $balanceService
    ) {}

    public function scoring(RoyxatCreditHistoryInputDTO $dto): ScoringRequest
    {
        $model = new ScoringRequest;
        $model->type = ScoringRequestTypeEnum::ROYXAT_CREDIT_HISTORY;
        $model->request = $dto;
        $model->client_code = $this->royxatClient->getClientCode();
        $model->status = ScoringRequestStatusEnum::WAITING;
        $model->saveOrFail();
        $this->balanceService->chargeAmount(FeatureEnum::SCORING_ROYXAT_CREDIT_HISTORY, $model);
        try {
            $royxatCreditHistoryDto = $this->royxatClient->getCredits($dto);

            if ($royxatCreditHistoryDto->isSuccess()) {
                $model->response = $royxatCreditHistoryDto;
                $model->status = ScoringRequestStatusEnum::SUCCESS;
            } else {
                throw new Exception($royxatCreditHistoryDto->description);
            }

        } catch (Exception $e) {
            Log::error($e);
            $model->status = ScoringRequestStatusEnum::FAILED;
        }
        $model->saveOrFail();

        return $model;
    }
}
