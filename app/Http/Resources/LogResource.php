<?php

namespace App\Http\Resources;

use App\Http\Resources\User\UserListResource;
use App\Models\Company\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ActivityLog */
class LogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'event' => $this->event->getLabel(),
            'subject_type' => $this->subject_type,
            'subject_id' => $this->subject_id,
            'user' => $this->whenLoaded('causer', function () {
                return UserListResource::make($this->causer);
            }),
            'properties' => $this->properties,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
