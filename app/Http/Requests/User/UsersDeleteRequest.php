<?php

namespace App\Http\Requests\User;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UsersDeleteRequest',
    title: 'Users delete request by ids',
    properties: [
        new OA\Property(property: 'ids', type: 'array', items: new OA\Items(type: 'int'), example: '[1, 2, 3]'),
    ],
)]
/**
 * @property int[] $ids
 */
class UsersDeleteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => 'array',
            'ids.*' => 'integer|exists:admins,id|not_in:' . auth()->id(),
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ADMIN_DELETE);
    }
}
