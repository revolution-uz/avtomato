<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AttachRoleRequest',
    title: 'Attach Role Request',
    required: ['role_ids'],
    properties: [
        new OA\Property(property: 'role_ids', type: 'array', items: new OA\Items(type: 'int', example: [1, 2])),
    ],
)]
class AttachRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
