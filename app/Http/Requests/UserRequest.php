<?php

namespace App\Http\Requests;

use App\Enums\Company\UserStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserRequest',
    title: 'User request body',
    properties: [
        new OA\Property(property: 'first_name', type: 'string', example: 'Tom'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Odell'),
        new OA\Property(property: 'username', type: 'string', example: 'admin'),
        new OA\Property(property: 'password', type: 'string', example: 'password'),
        new OA\Property(property: 'status', type: 'int', enum: [1, 0], example: 1),
    ],
)]
class UserRequest extends FormRequest
{
    public function rules(): array
    {
        $rules = [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'status' => ['required', 'integer', 'in:' . implode(',', UserStatusEnum::values())],
        ];

        // If request has user it means that we are updating the user
        if ($this->user) {
            // for updating the user we need to skip the unique validation for the current username
            $rules['username'] = 'required|string|max:50|unique:users,username,' . $this->user->id;
            $rules['password'] = 'nullable|string|min:8';
        } else {
            $rules['username'] = 'required|string|max:50|unique:users,username';
            $rules['password'] = 'required|string|min:8';
        }

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }
}
