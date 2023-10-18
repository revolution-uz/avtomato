<?php

namespace App\Services\Scoring;

use App\Clients\PlumClient;
use App\DTOs\Scoring\Plum\Mib\MibCreateRequestDTO;
use App\DTOs\Scoring\Plum\Tax\TaxCreateRequestDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;

class PlumService
{
    public function __construct(
        private readonly PlumClient $plumClient
    ) {
    }

    public function scoringMib(MibCreateRequestDTO $dto, ScoringRequestTypeEnum $type): ScoringRequest
    {
        $model = new ScoringRequest;
        $model->type = $type;
        $model->request = $dto;
        $model->client_code = $this->plumClient->getClientCode();
        $model->response = $this->plumClient->scoringMib(
            $dto->convertToPlumRequest()
        );
        $model->status = ScoringRequestStatusEnum::SUCCESS;
        $model->saveOrFail();

        return $model;
    }

    public function scoringTax(TaxCreateRequestDTO $dto, ScoringRequestTypeEnum $type): ScoringRequest
    {
        $model = new ScoringRequest;
        $model->type = $type;
        $model->request = $dto;
        $model->client_code = $this->plumClient->getClientCode();
        $model->response = $this->plumClient->scoringTax(
            $dto->convertToPlumRequest()
        );
        $model->status = ScoringRequestStatusEnum::SUCCESS;
        $model->saveOrFail();

        return $model;
    }
}
