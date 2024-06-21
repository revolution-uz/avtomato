<?php

namespace App\Http\Requests\Info;

use Illuminate\Foundation\Http\FormRequest;

class InfoSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
}
