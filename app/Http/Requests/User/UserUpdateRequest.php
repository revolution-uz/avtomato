<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserUpdateRequest',
    title: 'Profile update body',
    description: 'Profile update body',
    properties: [
        new OA\Property(property: 'first_name', type: 'string', example: 'Tom'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Odell'),
        new OA\Property(property: 'avatar', type: 'string', example: '9a37a080-2c00-4ec5-86ea-081f14d8987f'),
    ],
)]
class UserUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'first_name' => 'string|max:50',
            'last_name' => 'string|max:50',
            'avatar' => 'string|max:255',
        ];

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
