<?php

namespace App\Http\Requests;

use App\Rules\PhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ProfileRequest',
    title: 'Profile update body',
    properties: [
        new OA\Property(property: 'username', type: 'string', example: 'admin'),
        new OA\Property(property: 'phone', description: 'Send strongly in the format 998901234567', type: 'string', example: '+998901234567'),
        new OA\Property(property: 'first_name', type: 'string', example: 'Tom'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Odell'),
        new OA\Property(property: 'avatar', type: 'string', example: '5179f9c9-b46c-46ae-b5e0-fe0d3897e543'),
    ],
)]
class ProfileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => 'nullable|string|max:50|unique:users,username,' . $this->user()->id,
            'phone' => ['nullable', new PhoneRule()],
            'first_name' => 'nullable|string|max:50',
            'last_name' => 'nullable|string|max:50',
            'avatar' => 'nullable|uuid',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
