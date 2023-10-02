<?php

namespace App\Http\Resources;

use App\Models\Company\Rbac\PermissionGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin PermissionGroup */
class PermissionGroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->label,
            'permissions' => PermissionResource::collection($this->permissions),
        ];
    }
}
