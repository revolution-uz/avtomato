<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema()
 *
 * @OA\Property(property="id", type="int", example=1)
 * @OA\Property(property="name", type="string", example="Someone_Date")
 * @OA\Property(property="device_name", type="string", example="Google Pixel 7")
 * @OA\Property(property="device_ip", type="string", example="127.0.0.1")
 */
class DeviceInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this['id'],
            'name' => $this['name'],
            'device_name' => $this['device_name'],
            'device_ip' => $this['device_ip'],
        ];
    }
}
