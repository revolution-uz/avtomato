<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema()
 * @OA\Property(property="id", type="integer", example="1")
 * @OA\Property(property="phone", type="string", example="998977130104")
 * @OA\Property(property="identified", type="boolean", example="true")
 * @OA\Property(property="created_at", type="string", example="Y-m-d H:i:s")
 * @OA\Property(property="userInfo", type="object", ref="#/components/schemas/UserInfoResource")
 *
 * @property int $id
 * @property string $phone
 * @property mixed $created_at
 */
class ProfileResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'phone' => $this->phone,
            'identified' => $this->userInfo()->exists(),
            'userInfo' => UserInfoResource::make($this->whenLoaded('userInfo')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
