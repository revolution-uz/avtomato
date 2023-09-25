<?php

namespace App\Http\Resources;

use App\Enums\Company\UserStatusEnum;
use App\Models\Company\User;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserInfoResource',
    title: 'UserInfoResource',
    properties: [
        new OA\Property(property: 'username', type: 'string', example: 'admin'),
        new OA\Property(property: 'first_name', type: 'string', example: 'Yura'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Umarov'),
        new OA\Property(property: 'phone', type: 'string', example: '+998933993300'),
        new OA\Property(property: 'status', type: 'int', example: '1'),
        new OA\Property(property: 'avatar', type: 'string', example: 'https://i.pravatar.cc/300'),
    ],
)]
/** @mixin User */
class UserInfoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'username' => $this->username,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'phone' => $this->phone,
            'status' => $this->status,
            'avatar' => $this->avatar_url,
            'template_theme' => $this->template_theme,
            'language' => $this->language,
        ];
    }
}
