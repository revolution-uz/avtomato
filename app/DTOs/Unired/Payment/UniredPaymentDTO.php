<?php

namespace App\DTOs\Unired\Payment;

use App\DTOs\Unired\Base\BaseUniredDTO;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

/**
 * Class UniredPaymentDTO
 *
 * @property string $pinfl
 * @property string $loan_id
 * @property string $transaction_id
 * @property string $ext_id
 * @property string $card_mask
 * @property string $card_owner
 * @property int $amount
 * @property Carbon $date
 * @property string $terminal
 * @property string $merchant
 * @property int $filial_id
 * @property string $type
 * @property string $status
 * @property Carbon|null $refunded_at
 * @property Carbon $created_at
 * @property bool $is_sent
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
