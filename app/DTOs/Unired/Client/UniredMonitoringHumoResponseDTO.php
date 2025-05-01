<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class UniredMonitoringHumoResponseDTO extends BaseUniredDTO
{
    public function __construct(
        #[DataCollectionOf(UniredMonitoringHumoRowDTO::class)]
        public ?Collection $rows,
    ) {}
}
