<?php

namespace App\Http\Resources\User;

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
        new OA\Property(property: 'permissions', type: 'array', items: new OA\Items()),
    ],
)]
/** @mixin Role */
class SingleRoleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'permissions' => $this->mapRolePermissionByBranch(),
            'created_at' => $this->created_at ? (int)$this->created_at->format('U') : null,
            'updated_at' => $this->updated_at ? (int)$this->updated_at->format('U') : null,
        ];
    }
}
