<?php

namespace App\Services\Scoring;

use App\Clients\AsaxiyClient;
use App\DTOs\Scoring\Card\CardCreateRequestDTO;
use App\DTOs\Scoring\Card\CardScoringResponseDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;

class CardScoringService
{
    public function __construct(
        private readonly AsaxiyClient $asaxiyClient
    ) {
    }

    public function scoringHumo(CardCreateRequestDTO $dto, ScoringRequestTypeEnum $type): ScoringRequest
    {
        $model = new ScoringRequest;
        $model->type = $type;
        $model->request = $dto;
        $model->client_code = $this->asaxiyClient->getClientCode();
        $model->status = ScoringRequestStatusEnum::WAITING;
        $model->saveOrFail();
        $model->response = CardScoringResponseDTO::fromUnired($this->asaxiyClient->scoringHumo(
            $dto->toArray()
        ));

        $model->status = ScoringRequestStatusEnum::SUCCESS;
        $model->saveOrFail();

        return $model;
    }

    public function scoringUzkard(CardCreateRequestDTO $dto, ScoringRequestTypeEnum $type): ScoringRequest
    {
        $model = new ScoringRequest;
        $model->type = $type;
        $model->request = $dto;
        $model->client_code = $this->asaxiyClient->getClientCode();
        $model->status = ScoringRequestStatusEnum::WAITING;
        $model->saveOrFail();

        $model->response = CardScoringResponseDTO::fromUnired($this->asaxiyClient->scoringUzkard(
            $dto->toArray()
        ));

        $model->status = ScoringRequestStatusEnum::SUCCESS;
        $model->saveOrFail();

        return $model;
    }
}
