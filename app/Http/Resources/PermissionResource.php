<?php

namespace App\Http\Resources;

use App\Models\Company\Rbac\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @property Permission $resource */
class PermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'slug' => $this->resource->slug,
            'name' => $this->resource->name,
            'is_branch_related' => $this->resource->is_branch_related,
        ];
    }
}
