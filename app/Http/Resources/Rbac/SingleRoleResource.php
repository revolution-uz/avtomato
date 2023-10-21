<?php

namespace App\Http\Resources\Rbac;

use App\Models\Company\Rbac\Role;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SingleRoleResource',
    title: 'Single Role Resource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'owner'),
        new OA\Property(property: 'permission_groups', type: 'array', items: new OA\Items),
        new OA\Property(property: 'created_at', type: 'string', example: '1697561193'),
        new OA\Property(property: 'updated_at', type: 'string', example: '1697561193'),
    ],
)]
/** @property Role $resource */
class SingleRoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'permission_groups' => $this->resource->mapAllPermissionByBranch(),
            'created_at' => $this->resource->created_at?->format('U'),
            'updated_at' => $this->resource->updated_at?->format('U'),
        ];
    }
}
