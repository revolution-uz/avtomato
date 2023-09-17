<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema()
 *
 * @OA\Property(property="device_type", type="string", example="IOS / Android")
 * @OA\Property(property="device_name", type="string", example="Google Pixel 7")
 * @OA\Property(property="device_ip", type="string", example="127.0.0.1")
 * @OA\Property(property="location", type="string", example="Tashkent")
 * @OA\Property(property="app_version", type="string", example="1.0.7")
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
            'device_type' => $this['device_type'],
            'device_name' => $this['device_name'],
            'device_ip' => $this['device_ip'],
            'location' => $this['location'],
            'app_version' => $this['app_version'],

        ];
    }
}
