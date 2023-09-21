<?php

namespace App\Http\Resources\Admin;

use App\Enums\Company\UserStatus;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema()
 * @OA\Property(property="first_name", type="string", example="Furqat")
 * @OA\Property(property="last_name", type="string", example="Umarov")
 * @OA\Property(property="middle_name", type="string", example="Furqatovich")
 * @OA\Property(property="birth_date", type="string", example="2022-09-02")
 * @OA\Property(property="pinfl", type="string", example="12301203123")
 * @OA\Property(property="pass_data", type="string", example="AB 00000000")
 * @OA\Property(property="permanent_address", type="string", example="Toshkent")
 * @OA\Property(property="doc_type", type="string", example="Doc type")
 *
 * @property int $id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property UserStatus $status
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
    public function toArray($request)
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
