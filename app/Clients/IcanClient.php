<?php

namespace App\Clients;

use App\DTOs\Ican\IcanCreditDTO;
use App\DTOs\Ican\IcanMetaDTO;
use App\DTOs\Ican\IcanProfileDTO;
use App\DTOs\Ican\IcanTransactionDTO;
use App\DTOs\Ican\Request\IcanTransactionRequestDTO;
use App\Enums\ClientCodeEnum;
use App\Enums\External\IcanTransactionStatusEnum;
use Exception;
use Illuminate\Support\Facades\Log;

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

    public function getCredits(int $page = 0, int $pageSize = 100): array
    {
        $data = [
            'page' => $page,
            'page-size' => $pageSize,
            'with' => ['tariff', 'person_main', 'person_main.user_document'],
        ];

        try {
            $response = $this->makeRequest(self::CREDITS, $data, 'get');

            return [
                'credits' => IcanCreditDTO::collect($response['data']),
                'meta' => IcanMetaDTO::from($response['meta']),
            ];

        } catch (Exception $e) {
            Log::error($e);

            return ['credits' => null, 'meta' => null];
        }
    }

    public function getTransactions(IcanTransactionStatusEnum $status, int $page = 0, int $pageSize = 100): array
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
            $response = $this->makeRequest(self::TRANSACTIONS, $data, 'get');

            return [
                'transactions' => collect($response['data'])->map([IcanTransactionDTO::class, 'from']),
                'meta' => IcanMetaDTO::from($response['meta']),
            ];

        } catch (Exception $e) {
            return ['transactions' => null, 'meta' => null];
        }
    }

    public function createTransaction(IcanTransactionRequestDTO $dto): ?IcanTransactionDTO
    {
        try {
            return IcanTransactionDTO::from($this->makeRequest(self::TRANSACTIONS_CREATE, $dto->toArray()));

        } catch (Exception $e) {
            return null;
        }
    }

    public function getSingleCredit(int $ican_id): ?IcanCreditDTO
    {
        $path = str_replace('{ican_id}', $ican_id, self::CREDITS_SINGLE);

        try {
            return IcanCreditDTO::from($this->makeRequest($path, [], 'get'));

        } catch (Exception $e) {
            return null;
        }
    }

    public function getProfile(): ?IcanProfileDTO
    {
        try {
            return IcanProfileDTO::from($this->makeAuthRequest(self::PROFILE, [], 'get'));

        } catch (Exception $e) {
            return null;
        }
    }
}
