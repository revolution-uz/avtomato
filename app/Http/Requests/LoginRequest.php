<?php

namespace App\Http\Requests;

use App\Models\Company\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\Schema()
 *
 * @property string $phone
 * @property string $password
 * @property string $device_name
 * @property string $device_ip
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
            'phone' => ['bail', 'required', function ($attribute, $value, $fail) {
                $user = User::query()->where('phone', $this->phone)->first();
                if ($user === null) {
                    $fail(__('Telefon raqam xato.'));
                }
            }],
            'password' => ['bail', 'required', function ($attribute, $value, $fail) {
                $user = User::query()->where('phone', $this->phone)->first();
                if ($user === null) {
                    $fail(__('Telefon raqam xato.'));
                    return;
                }
                if (!Hash::check($value, $user->password)) {
                    $fail(__('Parol xato.'));
                }
            }],
            'device_name' => 'nullable|string',
            'device_ip' => 'nullable|string',
        ];
    }
}
