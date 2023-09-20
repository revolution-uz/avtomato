<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema()
 *
 * @property int $id
 */
class RevokeDeviceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @OA\Property(property="id", type="int", example=1)
     *
     * @return array<string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'int']
        ];
    }

}