<?php

namespace App\Services;

use App\Clients\QrPayClient;
use App\DTOs\QrPay\Terminal\SbpAccountResponseDTO;
use App\DTOs\QrPay\Terminal\SbpCreateAccountRequestDTO;
use App\Models\SbpAccount;

class QrPayService
{
    public function __construct(private QrPayClient $qrPayClient) {}

    public function addTerminal(SbpAccount $terminal): bool
    {
        $sbpResponseDto = $this->qrPayClient->createAccount(SbpCreateAccountRequestDTO::from($terminal));
        $terminalDto = SbpAccountResponseDTO::from($sbpResponseDto->result);
        $terminal->terminal_id = $terminalDto->id;
        $terminal->status = $terminalDto->status;

        return $terminal->saveOrFail();
    }
}
