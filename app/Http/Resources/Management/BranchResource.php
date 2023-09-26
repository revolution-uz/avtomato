<?php

namespace App\Http\Resources\Management;

use App\Models\Company\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BranchResource',
    title: 'BranchResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'name', type: 'string', example: '2-filial'),
        new OA\Property(property: 'address', type: 'string', example: 'Urganch shaxar'),
        new OA\Property(property: 'inn', type: 'string', example: '520212090'),
        new OA\Property(property: 'created_at', type: 'integer', example: 1695643479),
        new OA\Property(property: 'updated_at', type: 'integer', example: 1695643479),
    ],
)]
/**
 * use @mixin Branch
 */
class BranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'inn' => $this->inn,
            'created_at' => (int) $this->created_at->format('U'),
            'updated_at' => (int) $this->updated_at->format('U'),
        ];
    }
}
