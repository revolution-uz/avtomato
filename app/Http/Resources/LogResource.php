<?php

namespace App\Http\Resources;

use App\Http\Resources\User\UserListResource;
use App\Models\Company\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LogResource',
    title: 'LogResource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'event', type: 'string', example: 'Created'),
        new OA\Property(property: 'subject_type', type: 'string', example: 'App\Models\Company\Employee'),
        new OA\Property(property: 'subject_id', type: 'int', example: '1'),
        new OA\Property(property: 'user', ref: '#/components/schemas/UserListResource', description: 'User who performed the action'),
        new OA\Property(property: 'properties', type: 'object', example: ['attributes' => ['name' => 'John Doe'], 'old' => ['name' => 'Jane Doe new']]),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2021-08-31T12:00:00Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2021-08-31T12:00:00Z'),
    ],
)]
/** @property ActivityLog $resource */
class LogResource extends JsonResource
{
    public static function logMessageBuilder($modelName, $modelId, $event)
    {
        $modelName = trans('models.' . $modelName);

        return trans('logging.' . $event, ['entity' => $modelName, 'id' => $modelId]);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'user' => $this->whenLoaded('causer', function () {
                return UserListResource::make($this->resource->causer);
            }),
            'properties' => $this->resource->properties,
            'created_at' => $this->resource->created_at?->format('U'),
            'description' => self::logMessageBuilder($this->resource->subject_type, $this->resource->subject_id, $this->resource->event->value),
        ];
    }
}
