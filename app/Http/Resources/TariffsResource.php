<?php

namespace App\Http\Resources;

use App\Models\Company\Tariff;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema()
 *
 * @OA\Property(property="id", type="int", example=1)
 * @OA\Property(property="name", type="string", example="Tariff Name")
 * @OA\Property(property="is_annual", type="boolean", example="true")
 * @OA\Property(property="percent", type="double", example="40")
 * @OA\Property(property="min_amount", type="int", example="0")
 * @OA\Property(property="max_amount", type="int", example="10000000")
 * @OA\Property(property="min_initial_fee", type="int", example="0")
 * @OA\Property(property="max_initial_fee", type="int", example="10000000")
 * use @mixin Tariff
 */
class TariffsResource extends JsonResource
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
            'name' => $this->name,
            'is_annual' => $this->is_annual,
            'percent' => $this->percent,
            'min_amount' => $this->min_amount,
            'max_amount' => $this->max_amount,
            'min_initial_fee' => $this->min_initial_fee,
            'max_initial_fee' => $this->max_initial_fee,
        ];
    }
}
