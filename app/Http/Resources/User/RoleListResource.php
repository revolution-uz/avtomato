<?php

namespace App\Http\Resources\User;

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
    ],
)]
/** @mixin Role */
class RoleListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
