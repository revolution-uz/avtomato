<?php

namespace App\Services\Scoring;

use App\Clients\PlumClient;
use App\DTOs\Scoring\Plum\Mib\MibCreateRequestDTO;
use App\DTOs\Scoring\Plum\Mib\MibHistoryResponseDTO;
use App\DTOs\Scoring\Plum\Tax\TaxCreateRequestDTO;
use App\DTOs\Scoring\Plum\Tax\TaxHistoryResponseDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\Card;
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
        $model->status = ScoringRequestStatusEnum::WAITING;
        $model->saveOrFail();
        $model->response = MibHistoryResponseDTO::from($this->plumClient->scoringMib(
            $dto->toArray()
        ));

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
        $model->status = ScoringRequestStatusEnum::WAITING;
        $model->saveOrFail();

        $model->response = TaxHistoryResponseDTO::from($this->plumClient->scoringTax(
            $dto->toArray()
        ));

        $model->status = ScoringRequestStatusEnum::SUCCESS;
        $model->saveOrFail();

        return $model;
    }

    public function attachCard(Card $card, $phone)
    {
        return $this->plumClient->attachCard([
            'userId' => $card->customer_id,
            'cardNumber' => $card->number,
            'expireDate' => $card->expiry,
            'userPhone' => $phone,
        ]);
    }

    public function confirmCard(Card $card, $session, $otp)
    {
        $response = $this->plumClient->confirmCard([
            'session' => $session,
            'otp' => $otp,
            'isTrusted' => true,
        ]);

        $_card = $response['card'] ?? [];
        $plum_id = $_card['id'] ?? null;
        if ($plum_id) {
            $card->plum_id = $plum_id;

            return $card->saveOrFail();
        }

        return false;
    }

    public function getCards($customer_id)
    {
        return $this->plumClient->getCards([
            'userId' => $customer_id,
        ]);
    }
}
