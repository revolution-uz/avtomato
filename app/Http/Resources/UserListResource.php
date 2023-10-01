<?php

namespace App\Http\Resources;

use App\Models\Company\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserListResource',
    title: 'User List Resource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: 1),
        new OA\Property(property: 'username', type: 'string', example: 'admin'),
        new OA\Property(property: 'first_name', type: 'string', example: 'Yura'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Umarov'),
        new OA\Property(property: 'phone', type: 'string', example: '+998933993300'),
        new OA\Property(property: 'status', type: 'int', example: '1'),
        new OA\Property(property: 'avatar', ref: '#/components/schemas/AttachmentResource'),
    ],
)]
/**
 * @property User $resource
 */
class UserListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'username' => $this->resource->username,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'phone' => $this->resource->phones()->latest()->first()->phone ?? null,
            'status' => $this->resource->status,
            'avatar' => AttachmentResource::make($this->resource->avatars()->latest()->first()),
        ];
    }
}
