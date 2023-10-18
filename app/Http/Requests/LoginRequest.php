<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LoginRequest',
    title: 'Request to sign in',
    properties: [
        new OA\Property(property: 'username', type: 'string', example: 'admin'),
        new OA\Property(property: 'password', type: 'string', example: 'password'),
    ],
)]
/**
 * @property string $username
 * @property string $password
 */
class LoginRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['bail', 'required'],
            'password' => ['bail', 'required'],
        ];
    }
}
