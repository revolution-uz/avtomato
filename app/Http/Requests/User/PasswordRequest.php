<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PasswordRequest',
    title: 'Password update body',
    properties: [
        new OA\Property(property: 'password', type: 'string', example: 'password'),
        new OA\Property(property: 'password_confirmation', type: 'string', example: 'password'),
    ],
)]
class PasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
