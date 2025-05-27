<?php

namespace App\Http\Requests\QrPay;

use App\DTOs\QrPay\Payment\SbpPaymentDTO;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property SbpPaymentDTO $payment
 */
class SbpPaymentWebhookCallbackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'payment' => 'required|array',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
