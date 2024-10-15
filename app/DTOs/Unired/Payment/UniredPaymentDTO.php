<?php

namespace App\DTOs\Unired\Payment;

use App\DTOs\Unired\Base\BaseUniredDTO;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

/**
 * Class UniredRegisterPaymentRequestDTO
 *
 * @property string $client_ext_id
 * @property string $payment_ext_id
 * @property string $payment_transaction_id
 * @property int $amount
 */
class UniredPaymentDTO extends BaseUniredDTO
{
    public function __construct(
        public string $pinfl,
        public string $loan_id,
        public string $transaction_id,
        public string $ext_id,
        public string $card_mask,
        public string $card_owner,
        public int $amount,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $date,
        public string $terminal,
        public string $merchant,
        public int $filial_id,
        public string $type,
        public string $status,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public ?Carbon $refunded_at,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.u\Z')]
        public Carbon $created_at,
        public bool $is_sent,
        // public UniredCardDTO $card
        // public UniredBranchDTO $filial
    ) {}
}
