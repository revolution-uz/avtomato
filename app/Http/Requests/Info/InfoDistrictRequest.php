<?php

namespace App\Http\Requests\Info;

use Illuminate\Foundation\Http\FormRequest;

class InfoDistrictRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rule()
    {
        return [
            'region_id' => 'required|integer|exists:regions,id',
        ];
    }
}
