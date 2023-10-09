<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AvatarRequest',
    title: 'Avatar upload body',
    description: 'Avatar upload body. Send as multipart/form-data',
    properties: [
        new OA\Property(property: 'avatar', description: 'File to upload', type: 'file'),
    ],
)]
class AvatarRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'avatar' => 'required|file|mimes:png,jpg,jpeg|max:10240',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
