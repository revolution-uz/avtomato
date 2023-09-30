<?php

namespace App\Http\Resources\Common;

use App\Models\Company\Mahalla;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MahallaResource',
    title: 'MahallaResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '15'),
        new OA\Property(property: 'district_id', type: 'int', example: '3'),
        new OA\Property(property: 'name', type: 'string', example: 'Hamid Olimjon MFY'),
    ],
)]
/**
 * use @mixin Mahalla
 */
class MahallaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'district_id' => $this->district_id,
            'name' => $this->{'name_' . app()->getLocale()},
        ];
    }
}
