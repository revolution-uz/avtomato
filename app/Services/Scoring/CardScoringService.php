<?php

namespace App\Services\Scoring;

use App\Clients\AsaxiyClient;
use App\DTOs\Scoring\Card\CardCreateRequestDTO;
use App\DTOs\Scoring\Card\CardScoringResponseDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use Exception;
use Illuminate\Support\Facades\Log;

class CardScoringService
{
    public function __construct(
        private readonly AsaxiyClient $asaxiyClient
    ) {
    }

    public function scoringHumo(CardCreateRequestDTO $dto): ScoringRequest
    {
        $model = new ScoringRequest;
        $model->type = ScoringRequestTypeEnum::HUMO;
        $model->request = $dto;
        $model->client_code = $this->asaxiyClient->getClientCode();
        $model->status = ScoringRequestStatusEnum::WAITING;
        $model->saveOrFail();
        try {
            $model->response = CardScoringResponseDTO::parseFromUnired(
                $this->asaxiyClient->scoringHumo(
                    $dto->toArray()
                )
            );
            $model->status = ScoringRequestStatusEnum::SUCCESS;
            $model->saveOrFail();
        } catch (Exception $e) {
            Log::error($e);
            $model->status = ScoringRequestStatusEnum::FAILED;
            $model->saveOrFail();
        }

        return $model;
    }

    public function scoringUzcard(CardCreateRequestDTO $dto): ScoringRequest
    {
        $model = new ScoringRequest;
        $model->type = ScoringRequestTypeEnum::UZCARD;
        $model->request = $dto;
        $model->client_code = $this->asaxiyClient->getClientCode();
        $model->status = ScoringRequestStatusEnum::WAITING;
        $model->saveOrFail();
        try {
            $model->response = CardScoringResponseDTO::parseFromUnired($this->asaxiyClient->scoringUzcard(
                $dto->toArray()
            ));
            $model->status = ScoringRequestStatusEnum::SUCCESS;
            $model->saveOrFail();
        } catch (Exception $e) {
            Log::error($e);
            $model->status = ScoringRequestStatusEnum::FAILED;
            $model->saveOrFail();
        }

        return $model;
    }
}
