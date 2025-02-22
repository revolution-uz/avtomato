<?php

namespace App\Services;

use App\Clients\UniredSbpQRPayClient;
use App\DTOs\QrPay\Terminal\SbpAccountResponseDTO;
use App\DTOs\QrPay\Terminal\SbpCreateAccountRequestDTO;
use App\Models\SbpAccount;

class QrPayService
{
    public function __construct(private UniredSbpQRPayClient $qrPayClient) {}

    public function addAccount(SbpAccount $terminal): bool
    {
        $sbpResponseDto = $this->qrPayClient->createAccount(new SbpCreateAccountRequestDTO($terminal->account_number, $terminal->mfo, $terminal->inn, $terminal->company_name));
        $terminalDto = SbpAccountResponseDTO::from($sbpResponseDto->result);
        $terminal->terminal_id = $terminalDto->id;
        $terminal->status = $terminalDto->status;

        return $terminal->saveOrFail();
    }

    public function deleteAccount(SbpAccount $terminal)
    {
        $sbpResponseDto = $this->qrPayClient->deactivateAccount($terminal->terminal_id);
        $terminalDto = SbpAccountResponseDTO::from($sbpResponseDto->result);
        $terminal->status = $terminalDto->status;

        return $terminal->saveOrFail();
    }
}
