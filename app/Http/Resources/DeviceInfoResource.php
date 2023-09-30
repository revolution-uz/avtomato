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
        new OA\Property(property: 'last_used_at', type: 'integer', example: 1695643479),
    ],
)]
/**
 * use @mixin PersonalAccessToken
 */
class DeviceInfoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'device_ip' => $this->device_ip,
            'last_used_at' => $this->last_used_at ? (int)$this->last_used_at->format('U') : null,
        ];
    }
}
