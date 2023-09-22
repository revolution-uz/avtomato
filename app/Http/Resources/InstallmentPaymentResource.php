<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema()
 *
 * @OA\Property(property="id", type="int", example=1)
 * @OA\Property(property="date", type="string", example="Date")
 * @OA\Property(property="amount", type="float", example="120000")
 * @OA\Property(property="portion", type="float", example="4800")
 * @OA\Property(property="monthly", type="float", example="14800")
 *
 * @param int|null $id
 * @param string|null $date
 * @param float|null $amount
 * @param float|null $portion
 * @param float|null $monthly
 *
 */
class InstallmentPaymentResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'amount' => $this->amount,
            'portion' => $this->portion,
            'monthly' => $this->monthly,
        ];
    }
}
