<?php

namespace App\DTOs\Unired\Branch;

use App\DTOs\Unired\Base\BaseUniredDTO;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

/**
 * Class UniredBranchResponseDTO
 *
 * @property int $id
 * @property string $name
 * @property string $merchant_uzcard
 * @property string $terminal_uzcard
 * @property string $merchant_humo
 * @property string $terminal_humo
 * @property string $phone
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class UniredBranchResponseDTO extends BaseUniredDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $merchant_uzcard,
        public string $terminal_uzcard,
        public string $merchant_humo,
        public string $terminal_humo,
        public string $phone,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.u\Z')]
        public ?Carbon $created_at,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.u\Z')]
        public ?Carbon $updated_at,
    ) {}
}
