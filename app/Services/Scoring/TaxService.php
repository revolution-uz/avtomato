<?php

namespace App\Services\Scoring;

use App\Clients\AsaxiyClient;
use App\DTOs\Scoring\Plum\Tax\TaxInfoCreateRequestDTO;
use App\DTOs\Scoring\Plum\Tax\TaxInfoResponseDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\ScoringRequest;
use App\Services\User\BalanceService;
use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;

readonly class TaxService
{
    public function __construct(
        private AsaxiyClient $taxScoringClient,
        private BalanceService $balanceService
    ) {}

    /**
     * @throws Throwable
     */
    public function scoringTax(TaxInfoCreateRequestDTO $dto): ScoringRequest
    {
        $model = new ScoringRequest;
        $model->type = ScoringRequestTypeEnum::PLUM_TAX_INFO;
        $model->request = $dto;
        $model->client_code = $this->taxScoringClient->getClientCode();
        $model->status = ScoringRequestStatusEnum::WAITING;
        $model->saveOrFail();
        $this->balanceService->chargeAmount(FeatureEnum::SCORING_TAX_INFO, $model);
        try {
            $model->response = TaxInfoResponseDTO::from($this->taxScoringClient->scoringTax($dto->toArray()));
            $model->status = ScoringRequestStatusEnum::SUCCESS;
        } catch (Exception $e) {
            Log::error($e);
            $model->status = ScoringRequestStatusEnum::FAILED;
        }
        $model->saveOrFail();

        return $model;
    }
}
