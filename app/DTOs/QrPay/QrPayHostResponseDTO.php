<?php

namespace App\DTOs\QrPay;

use App\DTOs\QrPay\Base\BaseQrPayDTO;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

/**
 * Class QrPayHostResponseDTO
 *
 * @property string|null $host
 * @property Carbon|null $timestamp
 */
class QrPayHostResponseDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?string $host,
        #[WithCast(DateTimeInterfaceCast::class, format: ['Y-m-d H:i:s', 'Y-m-d H:i:s.u', 'Y-m-d\TH:i:s.u'])]
        public ?Carbon $timestamp,
    ) {}
}
