<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

/**
 * Class UniredCardsResponseDTO
 *
 * @property int $count
 * @property UniredCardResponseDTO[] $data
 */
class UniredCardsResponseDTO extends BaseUniredDTO
{
    public function __construct(
        public int $count,
        #[DataCollectionOf(UniredCardResponseDTO::class)]
        public ?DataCollection $data
    ) {}
}
