<?php

namespace App\Services;

use App\Clients\UniredClient;
use App\DTOs\Unired\Payment\UniredCancelPaymentRequestDTO;
use App\Helpers\Utilities;
use App\Models\Company\Transaction;

class UniredService
{
    public function __construct(private UniredClient $uniredClient) {}

    public function reversePayment(Transaction $transaction): bool
    {
        $response = $this->uniredClient->cancelPayment(new UniredCancelPaymentRequestDTO(Utilities::generateIdByTenancy($transaction->id), $transaction->external_id));

        return false;
    }
}
