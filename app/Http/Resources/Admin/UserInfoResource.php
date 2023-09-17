<?php

namespace App\Http\Resources\Admin;

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
 * @property string $name
 * @property string $last_name
 * @property string $middle_name
 * @property string $birth_date
 * @property string $pinfl
 * @property string $pass_data
 * @property string $permanent_address
 * @property string $doc_type
 */
class UserInfoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'doc_type' => $this->doc_type,
        ];
    }
}
