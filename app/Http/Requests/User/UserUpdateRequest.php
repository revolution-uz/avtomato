<?php

namespace App\Http\Requests\User;

use App\Rules\PhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserUpdateRequest',
    title: 'Profile update body',
    description: 'Profile update body. Send as multipart/form-data',
    properties: [
        new OA\Property(property: 'username', type: 'string', example: 'admin'),
        new OA\Property(property: 'phone', description: 'Send strongly in the format +998901234567', type: 'string', example: '+998901234567'),
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
            'phone' => [new PhoneRule()],
            'first_name' => 'string|max:50',
            'last_name' => 'string|max:50',
            'avatar' => 'string|max:255',
        ];

        // If the user is not set we are updating the auth user
        if ($this->user) {
            $rules['username'] = 'string|max:50|unique:users,username,' . $this->user->id;
        } else {
            $rules['username'] = 'string|max:50|unique:users,username,' . $this->user()->id;
        }

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
