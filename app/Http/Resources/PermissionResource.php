<?php

namespace App\Http\Resources;

use App\Http\Resources\User\SingleRoleResource;
use App\Models\Company\Rbac\Permission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Permission */
class PermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->label,
            'is_branch_related' => $this->is_branch_related,
        ];
    }
}
