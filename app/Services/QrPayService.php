<?php

namespace App\Services;

use App\Clients\QrPayClient;
use App\DTOs\QrPay\Base\BaseQrPayResponseDTO;
use App\DTOs\QrPay\Terminal\SbpAddTerminalRequestDTO;
use App\DTOs\QrPay\Terminal\SbpDeactivateTerminalRequestDTO;
use App\DTOs\QrPay\Terminal\SbpTerminalResponseDTO;
use App\DTOs\QrPay\Terminal\SbpUpdateTerminalRequestDTO;
use App\Enums\FeatureEnum;
use App\Models\Company\Feature;
use App\Models\SbpTerminal;

class QrPayService
{
    public function __construct(private QrPayClient $qrPayClient) {}

    public function addTerminal(SbpTerminal $terminal): bool
    {
        return $this->updateTerminalByResponse($terminal, $this->qrPayClient->addTerminal(SbpAddTerminalRequestDTO::from($terminal)));
    }

    public function updateTerminal(SbpTerminal $terminal): bool
    {
        return $this->updateTerminalByResponse($terminal, $this->qrPayClient->updateTerminal(SbpUpdateTerminalRequestDTO::from($terminal)));
    }

    public function deactivateTerminal(): bool
    {
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $sbpResponseDto = $this->qrPayClient->deactivateTerminal(new SbpDeactivateTerminalRequestDTO($feature->data['terminal_id']));
        $feature->data = array_merge($feature->data, SbpTerminalResponseDTO::from($sbpResponseDto->result)->toArray());

        return $feature->saveOrFail();
    }

    private function updateTerminalByResponse(SbpTerminal $terminal, BaseQrPayResponseDTO $sbpResponseDto): bool
    {
        $terminalDto = SbpTerminalResponseDTO::from($sbpResponseDto->result);
        $terminal->terminal_id = $terminalDto->terminal_id;
        $terminal->status = $terminalDto->status;

        return $terminal->saveOrFail();
    }
}
