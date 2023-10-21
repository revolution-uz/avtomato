<?php

namespace App\Http\Requests\Management;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BranchRequest',
    title: 'Request to create or update a branch',
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Branch Name'),
        new OA\Property(property: 'address', type: 'string', example: 'Branch Address'),
        new OA\Property(property: 'inn', type: 'string', example: 'Branch INN'),
    ],
)]
class BranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->id
            ? RbacHelper::checkPermission(PermissionEnum::BRANCH_EDIT)
            : RbacHelper::checkPermission(PermissionEnum::BRANCH_CREATE);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'inn' => 'required|string|max:9',
        ];
    }
}
