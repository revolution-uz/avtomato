<?php

namespace App\Http\Resources;

use App\Models\Company\Rbac\PermissionGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property PermissionGroup $resource */
class PermissionGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'slug' => $this->resource->slug,
            'name' => $this->resource->name,
            'permissions' => PermissionResource::collection($this->resource->permissions),
        ];
    }
}
