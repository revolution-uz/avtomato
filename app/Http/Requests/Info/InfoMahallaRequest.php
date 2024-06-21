<?php

namespace App\Http\Requests\Info;

use Illuminate\Foundation\Http\FormRequest;

class InfoMahallaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rule()
    {
        return [
            'district_id' => 'required|integer|exists:districts,id',
        ];
    }
}
