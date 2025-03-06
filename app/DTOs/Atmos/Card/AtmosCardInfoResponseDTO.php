<?php

namespace App\DTOs\Atmos\Card;

use App\DTOs\Atmos\AtmosResultDTO;
use Spatie\LaravelData\Data;

class AtmosCardInfoResponseDTO extends Data
{
    public function __construct(
        public int $id,
        public AtmosResultDTO $result,
        public AtmosInfoCardDTO $card,
        public AtmosBankDTO $bank,
    ) {}
}
