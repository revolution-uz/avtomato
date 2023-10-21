<?php

namespace App\Http\Requests;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserLangRequest',
    title: 'Request body to change language',
    properties: [
        new OA\Property(property: 'language', type: 'string', example: 'ru'),
    ],
)]
class UserLangRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'language' => 'required|string|max:2|in:ru,uz,oz,en',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::PROFILE_CHANGE_LANGUAGE);
    }
}
