<?php

namespace App\Http\Requests\Management;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BranchesDeleteRequest',
    title: 'Request to delete branches',
    properties: [
        new OA\Property(property: 'ids', type: 'array', items: new OA\Items(type: 'int'), example: '[1, 2, 3]'),
    ],
)]
/**
 * @property int[] $ids
 */
class BranchesDeleteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => 'array',
            'ids.*' => 'integer|exists:branches,id',
        ];
    }

    public function authorize()
    {
        return RbacHelper::checkPermission(PermissionEnum::BRANCH_DELETE);
    }
}
