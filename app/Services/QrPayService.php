<?php

namespace App\Services;

use App\Clients\QrPayClient;
use App\DTOs\QrPay\Base\BaseQrPayResponseDTO;
use App\DTOs\QrPay\Payment\SbpCancelPaymentRequestDTO;
use App\DTOs\QrPay\Payment\SbpCreatePaymentRequestDTO;
use App\DTOs\QrPay\Payment\SbpTransferRegistryRequestDTO;
use App\DTOs\QrPay\Terminal\SbpAddTerminalRequestDTO;
use App\DTOs\QrPay\Terminal\SbpDeactivateTerminalRequestDTO;
use App\DTOs\QrPay\Terminal\SbpTerminalResponseDTO;
use App\Enums\FeatureEnum;
use App\Helpers\Utilities;
use App\Models\Company\Feature;
use App\Models\Company\SbpTerminal;
use App\Models\Company\SbpTransaction;
use Carbon\Carbon;

class QrPayService
{
    public function __construct(private QrPayClient $qrPayClient) {}

    public function addTerminal(SbpTerminal $terminal): bool
    {
        return $this->updateTerminalByResponse($terminal, $this->qrPayClient->addTerminal(SbpAddTerminalRequestDTO::from($terminal)));
    }

    public function updateTerminal(SbpTerminal $terminal): bool
    {
        return $this->updateTerminalByResponse($terminal, $this->qrPayClient->updateTerminal(SbpAddTerminalRequestDTO::from($terminal)));
    }

    public function deactivateTerminal(): bool
    {
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $sbpResponseDto = $this->qrPayClient->deactivateTerminal(new SbpDeactivateTerminalRequestDTO($feature->data['terminal_id']));
        $feature->data = array_merge($feature->data, SbpTerminalResponseDTO::from($sbpResponseDto->result)->toArray());

        return $feature->saveOrFail();
    }

    public function createPayment(SbpTransaction $transaction): array
    {
        $uniredResponseDto = $this->qrPayClient->createPayment(new SbpCreatePaymentRequestDTO(Utilities::generateIdByTenancy($transaction->id), $transaction->cheque_amount, $transaction->cheque_purpose_code, $transaction->cheque_terminal_id, $transaction->cheque_redirect_url));

        return $uniredResponseDto->result;
    }

    public function statePayment(SbpTransaction $transaction): array
    {
        $uniredResponseDto = $this->qrPayClient->statePayment(new SbpCancelPaymentRequestDTO(Utilities::generateIdByTenancy($transaction->id)));

        return $uniredResponseDto->result;
    }

    public function cancelPayment(SbpTransaction $transaction): BaseQrPayResponseDTO
    {
        return $this->qrPayClient->cancelPayment(new SbpCancelPaymentRequestDTO(Utilities::generateIdByTenancy($transaction->id)));
    }

    public function transferRegistry(string $date): array
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        $uniredResponseDto = $this->qrPayClient->transferRegistry(new SbpTransferRegistryRequestDTO($date));

        return $uniredResponseDto->result;
    }

    private function updateTerminalByResponse(SbpTerminal $terminal, BaseQrPayResponseDTO $sbpResponseDto)
    {
        $terminalDto = SbpTerminalResponseDTO::from($sbpResponseDto->result);
        $terminal->terminal_id = $terminalDto->terminal_id;
        $terminal->status = $terminalDto->status;

        return $terminal->saveOrFail();
    }
}
