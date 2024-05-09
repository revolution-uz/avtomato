<?php

namespace App\Services\Scoring;

use App\Clients\PlumClient;
use App\DTOs\Scoring\Plum\Tax\TaxInfoCreateRequestDTO;
use App\DTOs\Scoring\Plum\Tax\TaxInfoResponseDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\ScoringRequest;
use App\Services\User\BalanceService;

class TaxService
{
    public function __construct(
        private readonly PlumClient $plumClient,
        private readonly BalanceService $balanceService
    ) {
    }

    public function scoringTax(TaxInfoCreateRequestDTO $dto): ScoringRequest
    {
        $model = new ScoringRequest;
        $model->type = ScoringRequestTypeEnum::PLUM_TAX_INFO;
        $model->request = $dto;
        $model->client_code = $this->plumClient->getClientCode();
        $model->status = ScoringRequestStatusEnum::WAITING;
        $model->saveOrFail();
        $this->balanceService->chargeAmount(FeatureEnum::SCORING_TAX_INFO, $model);

        $model->response = TaxInfoResponseDTO::from($this->plumClient->scoringTax(
            $dto->toArray()
        ));

        $model->status = ScoringRequestStatusEnum::SUCCESS;
        $model->saveOrFail();

        return $model;
    }
}
