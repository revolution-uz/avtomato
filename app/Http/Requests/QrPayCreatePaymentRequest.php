<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property float $amount
 */
class QrPayCreatePaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'numeric',
            ],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
