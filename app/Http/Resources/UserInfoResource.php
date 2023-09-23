<?php

namespace App\Http\Resources;

use App\Enums\Company\UserStatusEnum;
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
/**
 * @property int $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property UserStatusEnum $status
 * @property string $avatar
 * @property bool $is_dark_mode
 * @property bool $is_vertical_menu
 * @property bool $is_bordered_items
 * @property bool $is_fixed_navbar
 * @property bool $is_fixed_footer
 * @property bool $is_fixed_menu
 * @property bool $is_show_dropdown_on_hover
 * @property string $language
 */
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
            'avatar' => $this->avatar,
            'is_dark_mode' => $this->is_dark_mode,
            'is_vertical_menu' => $this->is_vertical_menu,
            'is_bordered_items' => $this->is_bordered_items,
            'is_fixed_navbar' => $this->is_fixed_navbar,
            'is_fixed_footer' => $this->is_fixed_footer,
            'is_fixed_menu' => $this->is_fixed_menu,
            'is_show_dropdown_on_hover' => $this->is_show_dropdown_on_hover,
            'language' => $this->language,
        ];
    }
}
