<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

/**
 * Class UniredCardResponseDTO
 *
 * @property string $pinfl
 * @property string $uuid
 * @property bool|null $auto
 * @property bool|null $is_verified
 * @property bool|null $is_blocked
 * @property string|null $block_reason
 * @property Carbon|null $blocked_at
 * @property string|null $owner
 * @property string|null $pan
 * @property string|null $phone
 */
class UniredCardResponseDTO extends BaseUniredDTO
{
    public function __construct(
        public string $pinfl,
        public string $uuid,
        public ?bool $auto,
        public ?bool $is_verified,
        public ?bool $is_blocked,
        public ?string $block_reason,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d H:i:s')]
        public ?Carbon $blocked_at,
        public ?string $owner,
        public ?string $pan,
        public ?string $phone
    ) {}
}
