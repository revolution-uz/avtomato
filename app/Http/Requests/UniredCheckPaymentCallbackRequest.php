<?php

namespace App\Http\Requests;

use App\Helpers\Utilities;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $loan_id
 * @property int $old_debt
 */
class UniredCheckPaymentCallbackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'loan_id' => 'required|string',
            'old_debt' => 'required|int',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function getCompanyId()
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->loan_id);

        return $arr[0] ?? null;
    }

    public function getClientId(): ?int
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->loan_id);

        return isset($arr[1]) ? (int) $arr[1] : null;
    }
}
