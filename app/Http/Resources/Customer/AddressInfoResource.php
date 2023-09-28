<?php

namespace App\Http\Resources\Customer;

use App\DTOs\OilaKredit\PassportDataDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AddressInfoResource',
    title: 'Address info by passport data',
    properties: [
        new OA\Property(property: 'address', type: 'string', example: 'Urganch shahar Г. УРГЕНЧ ЮКОРИ-БОГ'),
        new OA\Property(property: 'region_id', type: 'int', example: '1'),
        new OA\Property(property: 'district_id', type: 'int', example: '2'),
        new OA\Property(property: 'mahalla_id', type: 'int', example: '3', nullable: true),
    ],
)]
/**
 * use @mixin PassportDataDTO
 */
class AddressInfoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'address' => $this->address,
            'region_id' => $this->region_id,
            'district_id' => $this->district_id,
            'mahalla_id' => null,
        ];
    }
}
