<?php

namespace App\Http\Resources\Common;

use App\Models\Company\District;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DistrictResource',
    title: 'DistrictResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'name', type: 'string', example: 'Urganch shaxri'),
    ],
)]
/**
 * use @mixin District
 */
class DistrictResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->{'name_' . app()->getLocale()},
        ];
    }
}
