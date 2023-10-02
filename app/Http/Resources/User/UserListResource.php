<?php

namespace App\Http\Resources\User;

use App\Http\Resources\AttachmentResource;
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
/** @mixin User */
class UserListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phones()->latest()->first()->phone ?? null,
            'status' => $this->status,
            'avatar' => AttachmentResource::make($this->avatars()->latest()->first()),
        ];
    }
}
