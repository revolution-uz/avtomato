<?php

namespace App\Http\Resources\Rbac;

use App\Models\Company\Rbac\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RoleListResource',
    title: 'Role List Resource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'owner'),
        new OA\Property(property: 'created_at', type: 'string', example: '123456789'),
        new OA\Property(property: 'updated_at', type: 'string', example: '123456789'),
    ],
)]
/** @property Role $resource */
class RoleListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'created_at' => $this->resource->created_at?->format('U'),
            'updated_at' => $this->resource->updated_at?->format('U'),
        ];
    }
}
