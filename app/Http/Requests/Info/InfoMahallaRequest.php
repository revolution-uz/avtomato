<?php

namespace App\Http\Requests\Info;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;

class InfoMahallaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INFO_DISTRICTS);
    }

    public function rule()
    {
        return [
            'district_id' => 'required|integer|exists:districts,id',
        ];
    }
}
