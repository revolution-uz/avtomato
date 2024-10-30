<?php

namespace App\DTOs\Scoring\RoyxatCreditHistory;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class RoyxatCreditHistoryInputDTO extends BaseScoringRequest
{
    public function __construct(
        public ?string $pinfl,
        public ?string $tin,
        public ?string $passport_id,
    )
    {
    }
}
