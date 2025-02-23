<?php

namespace App\Services;

use App\Clients\UniredSbpQRPayClient;
use App\DTOs\QrPay\Terminal\SbpAccountResponseDTO;
use App\DTOs\QrPay\Terminal\SbpCreateAccountRequestDTO;
use App\Models\SbpAccount;

class QrPayService
{
    public function __construct(private UniredSbpQRPayClient $qrPayClient) {}

    public function addAccount(SbpAccount $sbpAccount): bool
    {
        $sbpResponseDto = $this->qrPayClient->createAccount(new SbpCreateAccountRequestDTO($sbpAccount->account_number, $sbpAccount->mfo, $sbpAccount->inn, $sbpAccount->company_name));
        if ($sbpResponseDto->isSuccess()) {
            $this->updateAccount($sbpAccount, SbpAccountResponseDTO::from($sbpResponseDto->result));
        }

        return false;
    }

    public function syncAccounts(): bool
    {
        $accounts = $this->qrPayClient->syncAccounts();
        if ($accounts->isSuccess()) {
            foreach ($accounts->result as $account) {
                $sbpAccount = SbpAccount::query()->where('terminal_id', $account->id)->first();
                if ($sbpAccount) {
                    $this->updateAccount($sbpAccount, SbpAccountResponseDTO::from($account));
                } else {
                    $sbpAccount = new SbpAccount;
                    $this->updateAccount($sbpAccount, SbpAccountResponseDTO::from($account));
                }
            }

            return true;
        }

        return false;
    }

    public function deleteAccount(SbpAccount $terminal)
    {
        $sbpResponseDto = $this->qrPayClient->deactivateAccount($terminal->terminal_id);
        $terminalDto = SbpAccountResponseDTO::from($sbpResponseDto->result);
        $terminal->status = $terminalDto->status;

        return $terminal->saveOrFail();
    }

    private function updateAccount(SbpAccount $sbpAccount, SbpAccountResponseDTO $responseAccount)
    {
        $sbpAccount->terminal_id = $responseAccount->id;
        $sbpAccount->status = $responseAccount->status;

        return $sbpAccount->saveOrFail();
    }
}
