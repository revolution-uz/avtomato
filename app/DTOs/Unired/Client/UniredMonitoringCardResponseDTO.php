<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class UniredMonitoringCardResponseDTO extends BaseUniredDTO
{
    public function __construct(
        #[DataCollectionOf(UniredMonitoringCardRowDTO::class)]
        public ?Collection $rows,
    ) {}
}
