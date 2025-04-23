<?php

namespace App\Clients;

use App\DTOs\Ican\IcanCreditsResponseDTO;
use App\DTOs\Ican\IcanProfileResponseDTO;
use App\DTOs\Ican\IcanSingleCreditResponseDTO;
use App\DTOs\Ican\IcanTransactionInputDTO;
use App\DTOs\Ican\IcanTransactionsResponseDTO;
use App\Enums\ClientCodeEnum;
use App\Enums\External\IcanTransactionStatusEnum;
use Exception;

class IcanClient extends BaseIcanClient
{
    const CREDITS = '/company/credit/credit';
    const CREDITS_SINGLE = '/company/credit/credit/{ican_id}';
    const TRANSACTIONS = '/company/credit/transaction';
    const TRANSACTIONS_CREATE = '/company/credit/transaction/create-manually';

    const PROFILE = '/common/user/profile';

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

    public function getTransactions(IcanTransactionStatusEnum $status, int $page = 0, int $pageSize = 100): IcanTransactionsResponseDTO
    {
        $data = [
            'page' => $page,
            'page-size' => $pageSize,
            'with' => [
                'credit',
                'credit.person_main.user_document',
                'payments',
            ],
            'filter' => [
                'status' => $status->value,
            ],
        ];

        try {
            return IcanTransactionsResponseDTO::from($this->makeRequest(self::TRANSACTIONS, $data, 'get'));

        } catch (Exception $e) {
            return IcanTransactionsResponseDTO::from(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function createTransaction(IcanTransactionInputDTO $dto): IcanTransactionsResponseDTO
    {
        try {
            return IcanTransactionsResponseDTO::from($this->makeRequest(self::TRANSACTIONS_CREATE, $dto->toArray(), 'post'));

        } catch (Exception $e) {
            return IcanTransactionsResponseDTO::from(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getSingleCredit(int $ican_id)
    {
        try {
            return IcanSingleCreditResponseDTO::from([
                'data' => $this->makeRequest(str_replace('{ican_id}', $ican_id, self::CREDITS_SINGLE), [], 'get'),
            ]);

        } catch (Exception $e) {
            return IcanSingleCreditResponseDTO::from(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getProfile()
    {
        try {
            return IcanProfileResponseDTO::from([
                'data' => $this->makeAuthRequest(self::PROFILE, [], 'get'),
            ]);

        } catch (Exception $e) {
            return IcanProfileResponseDTO::from(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
