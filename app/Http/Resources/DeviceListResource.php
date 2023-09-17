<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema()
 *
 * @OA\Property(property="id", type="string", example="c571ff3fd44ee2461aa3076f7b31b0fb43d3e83e2ca501550b9a71a9275a3ec235cc5114612bed07")
 * @OA\Property(property="info", type="array", @OA\Items(ref="#/components/schemas/DeviceInfoResource"))
 * @OA\Property(property="created_at", type="int", example="1684922315")
 * @OA\Property(property="current_session", type="bool", ref="true")
 *
 */
class DeviceListResource extends JsonResource
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
            "id" => $this['id'],
            "info" => DeviceInfoResource::make($this['info']),
            "created_at" => $this['created_at'],
            "current_session" => $this['current_session']
        ];
    }
}
