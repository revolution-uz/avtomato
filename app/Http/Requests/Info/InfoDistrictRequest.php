<?php

namespace App\Http\Requests\Info;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;

class InfoDistrictRequest extends FormRequest
{
    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INFO_DISTRICTS);
    }

    public function rule()
    {
        return [
            'region_id' => 'required|integer|exists:regions,id',
        ];
    }
}
