<?php

namespace App\Services\Scoring;

use App\Clients\AsaxiyClient;
use App\Clients\PlumClient;
use App\DTOs\Scoring\Plum\Mib\MibInfoCreateRequestDTO;
use App\DTOs\Scoring\Plum\Mib\MibInfoResponseDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\ScoringRequest;
use App\Services\User\BalanceService;

class MibService
{
    public function __construct(
        private readonly AsaxiyClient   $mibScoringClient,
        private readonly BalanceService $balanceService
    )
    {
    }

    public function scoringMib(MibInfoCreateRequestDTO $dto): ScoringRequest
    {
        $model = new ScoringRequest;
        $model->type = ScoringRequestTypeEnum::PLUM_MIB_INFO;
        $model->request = $dto;
        $model->client_code = $this->mibScoringClient->getClientCode();
        $model->status = ScoringRequestStatusEnum::WAITING;
        $model->saveOrFail();
        $this->balanceService->chargeAmount(FeatureEnum::SCORING_MIB_INFO, $model);

        $model->response = MibInfoResponseDTO::from($this->mibScoringClient->scoringMib(
            $dto->toArray()
        ));

        $model->status = ScoringRequestStatusEnum::SUCCESS;
        $model->saveOrFail();

        return $model;
    }
}
