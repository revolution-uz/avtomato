<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class UniredMonitoringUzcardResponseDTO extends BaseUniredDTO
{
    public function __construct(
        public ?int $total_elements,
        public ?int $number_of_elements,
        public ?int $total_pages, // pages count
        public ?int $page_number, // page index
        public ?int $total_debit,
        public ?int $total_credit,
        #[DataCollectionOf(UniredMonitoringUzcardContentDTO::class)]
        public ?Collection $content,
    ) {}
}
