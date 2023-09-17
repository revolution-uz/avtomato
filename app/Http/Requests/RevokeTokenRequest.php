<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema()
 *
 * @property string $token
 */
class RevokeTokenRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @OA\Property(property="token", type="string", example="c571ff3fd44ee2461aa3076f7b31b0fb43d3e83e2ca501550b9a71a9275a3ec235cc5114612bed07")
     *
     * @return array<string>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'max:80',function ($attribute, $value, $fail) {
                if($value == auth()->user()->token()->id){
                    $fail(__('messages.current token revoke error'));
                }
            }]
        ];
    }

}