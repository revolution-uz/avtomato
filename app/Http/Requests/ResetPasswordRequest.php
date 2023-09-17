<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Annotations as OA;
use Opencard\SharedServices\Rules\UzNumberRule;

/**
 * @OA\Schema()
 *
 * @property string $phone
 * @property string $password
 * @property string $confirmation_token
 */
class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @OA\Property(property="phone", type="string", example="998901112233")
     * @OA\Property(property="password", type="string", example="13%6egyY")
     * @OA\Property(property="password_confirmation", type="string", example="13%6egyY")
     * @OA\Property(
     *     property="confirmation_token",
     *     type="string",
     *     example="qIH2i6875IsBOe3wA2vIM06GSsvo6hb5OTdJLOjk",
     *     nullable=false,
     *     description="Token given when phone confirmed."
     * )
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'phone' => 'required','exists:users,phone',
            'password' => 'required|confirmed|min:6|max:124',
            'confirmation_token' => 'required|string',
        ];
    }
}
