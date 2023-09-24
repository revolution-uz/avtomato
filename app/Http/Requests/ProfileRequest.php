<?php

namespace App\Http\Requests;

use App\Rules\PhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProfileRequest',
    title: 'Profile update body',
    description: 'Profile update body. Send as multipart/form-data',
    properties: [
        new OA\Property(property: 'username', type: 'string', example: 'admin'),
        new OA\Property(property: 'phone', description: 'Send strongly in the format +998901234567', type: 'string', example: '+998901234567'),
        new OA\Property(property: 'first_name', type: 'string', example: 'Tom'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Odell'),
        new OA\Property(property: 'avatar', type: 'file'),
    ],
)]
class ProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => 'required|string|max:50|unique:users,username,' . $this->user()->id,
            'phone' => ['nullable', new PhoneRule(), 'unique:users,phone,' . $this->user()->id],
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'avatar' => 'nullable|image|max:3000',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
