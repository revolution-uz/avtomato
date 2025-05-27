<?php

namespace App\Http\Requests;

use App\Helpers\Utilities;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property array $messages
 * @property array $params
 */
class UniredSendPaymentSmsCallbackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'params' => 'required|array',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function getPhone()
    {
        return $this->params['phone'] ?? null;
    }

    public function getCompanyId()
    {
        $loan_id = $this->params['loan_id'] ?? null;
        $arr = explode(Utilities::TENANT_SEPARATOR, $loan_id);

        return $arr[0] ?? null;
    }

    public function getClientId(): ?int
    {
        $loan_id = $this->params['loan_id'] ?? null;
        $arr = explode(Utilities::TENANT_SEPARATOR, $loan_id);

        return isset($arr[1]) ? (int) $arr[1] : null;
    }

    public function getCardPan(): ?string
    {
        return $this->params['"pan'] ?? null;
    }
}
