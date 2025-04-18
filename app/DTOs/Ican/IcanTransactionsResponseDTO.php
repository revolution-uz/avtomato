<?php

namespace App\DTOs\Ican;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class IcanTransactionsResponseDTO extends BaseScoringRequest
{
    public function __construct(
        #[DataCollectionOf(IcanTransactionDTO::class)]
        public ?DataCollection $data,
        // links,
        public ?IcanMetaDTO $meta,
        // total,
        public bool $success = true,
        public ?string $message = null,
    ) {}

    public function isSuccess(): bool
    {
        return $this->success;
    }
}
