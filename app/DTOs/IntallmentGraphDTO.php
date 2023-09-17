<?php

namespace App\DTOs;

class IntallmentGraphDTO
{
    /**
     * @param int|null $id
     * @param int|null $date
     * @param float|null $amount
     * @param float|null $portion
     * @param float|null $monthly
     */
    public function __construct(
        public readonly ?int    $id = null,
        public readonly ?int $date = null,
        public readonly ?float  $amount = null,
        public readonly ?float  $portion = null,
        public readonly ?float  $monthly = null,
    )
    {
    }
}
