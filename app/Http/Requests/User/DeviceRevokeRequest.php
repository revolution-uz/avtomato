<?php

namespace App\Http\Requests\User;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DeviceRevokeRequest',
    title: 'Device revoke request by ids',
    properties: [
        new OA\Property(property: 'ids', type: 'array', items: new OA\Items(type: 'int'), example: '[1, 2, 3]'),
    ],
)]
/**
 * @property int[] $ids
 */
class DeviceRevokeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => 'array',
            'ids.*' => 'integer|exists:personal_access_tokens,id',
        ];
    }

    public function authorize(): bool
    {
        return match (Route::currentRouteName()) {
            'profile.revoke-device' => RbacHelper::checkPermission(PermissionEnum::PROFILE_REVOKE_DEVICE),
            'users.revoke-device' => RbacHelper::checkPermission(PermissionEnum::ADMIN_DEVICES),
            default => false
        };
    }
}
