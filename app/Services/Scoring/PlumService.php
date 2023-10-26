<?php

namespace App\Services\Scoring;

use App\Clients\PlumClient;
use App\DTOs\Scoring\Plum\Mib\MibInfoCreateRequestDTO;
use App\DTOs\Scoring\Plum\Mib\MibInfoResponseDTO;
use App\DTOs\Scoring\Plum\Tax\TaxInfoCreateRequestDTO;
use App\DTOs\Scoring\Plum\Tax\TaxInfoResponseDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;

class PlumService
{
    public function __construct(
        private readonly PlumClient $plumClient
    ) {
    }

    public function scoringMib(MibInfoCreateRequestDTO $dto, ScoringRequestTypeEnum $type): ScoringRequest
    {
        $model = new ScoringRequest;
        $model->type = $type;
        $model->request = $dto;
        $model->client_code = $this->plumClient->getClientCode();
        $model->status = ScoringRequestStatusEnum::WAITING;
        $model->saveOrFail();
        $model->response = MibInfoResponseDTO::from($this->plumClient->scoringMib(
            $dto->toArray()
        ));

        $model->status = ScoringRequestStatusEnum::SUCCESS;
        $model->saveOrFail();

        return $model;
    }

    public function scoringTax(TaxInfoCreateRequestDTO $dto, ScoringRequestTypeEnum $type): ScoringRequest
    {
        $model = new ScoringRequest;
        $model->type = $type;
        $model->request = $dto;
        $model->client_code = $this->plumClient->getClientCode();
        $model->status = ScoringRequestStatusEnum::WAITING;
        $model->saveOrFail();

        $model->response = TaxInfoResponseDTO::from($this->plumClient->scoringTax(
            $dto->toArray()
        ));

        $model->status = ScoringRequestStatusEnum::SUCCESS;
        $model->saveOrFail();

        return $model;
    }
}
