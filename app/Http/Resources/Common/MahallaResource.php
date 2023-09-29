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
        new OA\Property(property: 'id', type: 'int', example: '1'),
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
            'name' => $this->{'name_' . app()->getLocale()},
        ];
    }
}
