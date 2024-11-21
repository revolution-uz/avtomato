<?php

namespace App\Http\Requests;

use App\Helpers\Utilities;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $ext_id
 */
class SbpPaymentWebhookCallbackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ext_id' => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function getCompanyId()
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->ext_id);

        return $arr[0] ?? null;
    }

    public function getInstallmentId(): ?int
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->ext_id);

        return isset($arr[1]) ? (int) $arr[1] : null;
    }

    public function isGuarantorPayment(): bool
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->ext_id);

        return is_array($arr) && count($arr) === 3;
    }

    public function getInstallmentGuarantorCustomerId(): ?int
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->ext_id);

        return isset($arr[2]) ? (int) $arr[2] : null;
    }
}
