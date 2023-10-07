<?php

namespace App\Http\Requests\Rbac;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RolesDeleteRequest',
    title: 'Request to delete roles',
    properties: [
        new OA\Property(property: 'ids', type: 'array', items: new OA\Items(type: 'int'), example: '[1, 2, 3]'),
    ],
)]
/**
 * @property int[] $ids
 */
class RolesDeleteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => 'array',
            'ids.*' => 'integer|exists:roles,id|not_in:1',
        ];
    }

    public function messages()
    {
        return parent::messages() + [
            'ids.*.not_in' => 'You cannot delete the owner role',
        ];
    }
}
