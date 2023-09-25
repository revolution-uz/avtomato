<?php

namespace App\Http\Resources;

use App\Enums\Company\UserStatusEnum;
use App\Models\Company\User;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserResource',
    title: 'UserResource',
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
    ],
)]
/** @mixin User */
class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phones()->latest()->first()->phone ?? null,
            'status' => $this->status,
            'avatar' => AttachmentResource::make($this->avatars()->latest()->first()),
            'template_theme' => $this->template_theme,
            'language' => $this->language,
        ];
    }
}
