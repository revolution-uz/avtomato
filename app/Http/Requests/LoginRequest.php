<?php

namespace App\Http\Requests;

use App\Models\Company\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Schema()
 *
 * @property string $username
 * @property string $password
 * @property string $device_name
 */
class LoginRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'username' => ['bail', 'required'],
            'password' => ['bail', 'required'],
            'device_name' => 'required|string',
        ];
    }
}
