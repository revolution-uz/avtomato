<?php

namespace App\Clients;

use App\DTOs\Ican\IcanCreditsResponseDTO;
use App\DTOs\Ican\IcanTransactionsResponseDTO;
use App\Enums\ClientCodeEnum;
use App\Enums\FeatureEnum;
use Exception;

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
            ],
        ];

        try {
            return IcanCreditsResponseDTO::from($this->makeRequest(self::CREDITS, $data, 'get'));

        } catch (Exception $e) {
            return IcanCreditsResponseDTO::from(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getTransactions(int $page = 0, int $pageSize = 100): IcanTransactionsResponseDTO
    {
        $data = [
            'page' => $page,
            'page-size' => $pageSize,
            'with' => [
                'credit',
                'credit.person_main.user_document',
                'payments',
            ],
        ];

        try {
            return IcanTransactionsResponseDTO::from($this->makeRequest(self::TRANSACTIONS, $data, 'get'));

        } catch (Exception $e) {
            return IcanTransactionsResponseDTO::from(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
