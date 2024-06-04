<?php

namespace App\Http\Resources;

use App\Models\Company\PersonalAccessToken;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DeviceInfoResource',
    title: 'DeviceInfoResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'name', type: 'string', example: 'iPhone 15 Pro Max'),
        new OA\Property(property: 'device_ip', type: 'string', example: '192.168.0.1'),
        new OA\Property(property: 'device_type', type: 'string', example: '192.168.0.1'),
        new OA\Property(property: 'device_os', type: 'string', example: '192.168.0.1'),
        new OA\Property(property: 'last_used_at', type: 'integer', example: 1695643479),
    ],
)]
/**
 * @property PersonalAccessToken $resource
 */
class DeviceInfoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'device_ip' => $this->resource->device_ip,
            'device_type' => $this->resource->device_type,
            'device_os' => $this->resource->device_os,
            'last_used_at' => $this->resource->last_used_at ? (int) $this->resource->last_used_at->format('U') : null,
        ];
    }
}
