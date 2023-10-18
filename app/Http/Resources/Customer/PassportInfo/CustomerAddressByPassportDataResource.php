<?php

namespace App\Http\Resources\Customer\PassportInfo;

use App\DTOs\PassportDataDTO;
use App\Models\Company\District;
use App\Models\Company\Region;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CustomerAddressByPassportDataResource',
    title: 'Address info by passport data',
    properties: [
        new OA\Property(property: 'address', type: 'string', example: 'Urganch shahar Г. УРГЕНЧ ЮКОРИ-БОГ'),
        new OA\Property(property: 'region_id', type: 'int', example: '1'),
        new OA\Property(property: 'district_id', type: 'int', example: '2'),
        new OA\Property(property: 'mahalla_id', type: 'int', example: '3', nullable: true),
    ],
)]
/**
 * @property PassportDataDTO $resource
 */
class CustomerAddressByPassportDataResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $region = Region::where('oilakredit_id', $this->resource->region_id)->first();
        $district = District::where('oilakredit_id', $this->resource->district_id)->first();

        return [
            'address' => $this->resource->address,
            'region_id' => $region?->id,
            'district_id' => $district?->id,
            'mahalla_id' => null,
        ];
    }
}
