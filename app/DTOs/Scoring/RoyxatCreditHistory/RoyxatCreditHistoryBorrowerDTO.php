<?php

namespace App\DTOs\Scoring\RoyxatCreditHistory;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class RoyxatCreditHistoryBorrowerDTO extends BaseScoringRequest
{
    public function __construct(
        public ?string $full_name,
        public ?string $passport_id,
        public ?string $pinfl,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public ?Carbon $birth_date,
    ) {}
}
