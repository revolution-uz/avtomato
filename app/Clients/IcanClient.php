<?php

namespace App\Clients;

use App\DTOs\Ican\IcanCreditsResponseDTO;
use App\DTOs\Ican\IcanTransactionsResponseDTO;
use App\Enums\ClientCodeEnum;
use App\Enums\FeatureEnum;

class IcanClient extends BaseIcanClient
{
    const CREDITS = '/company/credit/credit';
    const TRANSACTIONS = '/company/credit/transaction';

    public function __construct()
    {
        $this->baseUrl = config('clients.ican.base_url');
        $this->channel = 'ican-client';
        $this->featureName = FeatureEnum::ICAN;
    }

    public function getClientCode(): ClientCodeEnum
    {
        return ClientCodeEnum::ICAN;
    }

    public function getCredits(int $page = 0, int $pageSize = 100): IcanCreditsResponseDTO
    {
        $data = [
            'page' => $page,
            'page-size' => $pageSize,
            'with' => [
                'tariff',
                'person_main',
                'person_main.user_document',
                'person_main.district.region',
            ],
        ];

        return IcanCreditsResponseDTO::from($this->makeRequest(self::CREDITS, $data, 'get'));
    }

    public function getTransactions(int $page = 0, int $pageSize = 100): IcanTransactionsResponseDTO
    {
        $data = [
            'page' => $page,
            'page-size' => $pageSize,
        ];

        return IcanTransactionsResponseDTO::from($this->makeRequest(self::TRANSACTIONS, $data, 'get'));
    }
}
