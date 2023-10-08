<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Rbac\RoleListResource;
use App\Models\Company\User;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SingleUserResource',
    title: 'SingleUserResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: 1),
        new OA\Property(property: 'username', type: 'string', example: 'admin'),
        new OA\Property(property: 'first_name', type: 'string', example: 'Yura'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Umarov'),
        new OA\Property(property: 'phone', type: 'string', example: '+998933993300'),
        new OA\Property(property: 'status', type: 'int', example: '1'),
        new OA\Property(property: 'avatar', ref: '#/components/schemas/AttachmentResource'),
        new OA\Property(property: 'template_theme', ref: '#/components/schemas/UserThemeRequest'),
        new OA\Property(property: 'language', type: 'string', example: 'ru'),
        new OA\Property(property: 'role', ref: '#/components/schemas/RoleListResource'),
        new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(type: 'object', example: [
            'view_user' => [
                'branch_1' => true,
                'branch_2' => true,
                'all' => false,
            ],
            'edit_user' => [
                'branch_1' => false,
                'branch_2' => false,
                'all' => true,
            ],
        ])),
    ],
)]
/**
 * @property User $resource
 */
class SingleUserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'username' => $this->resource->username,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'phone' => $this->resource->phones()->latest()->first()->phone ?? null,
            'status' => $this->resource->status,
            'avatar' => $this->resource->avatar,
            'template_theme' => $this->resource->template_theme,
            'language' => $this->resource->language,
            'role' => RoleListResource::make($this->resource->role),
            'permissions' => $this->resource->role?->mapRolePermissionByBranch(),
        ];
    }
}
