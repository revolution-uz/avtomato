<?php

namespace App\Http\Requests;

use App\Helpers\Utilities;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $pinfl
 * @property string $loan_id
 * @property string $ext
 * @property array $card
 * @property int $amount
 * @property Carbon $date
 * @property string $terminal
 * @property string $merchant
 * @property int $merchant_id
 * @property string $status
 * @property Carbon $created_at
 */
class UniredPaymentWebhookCallbackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pinfl' => 'required|string',
            'loan_id' => 'required|string',
            'ext' => 'required|string',
            'card' => 'required|array',
            'amount' => 'required|int',
            'date' => 'required|string',
            'terminal' => 'required|string',
            'merchant' => 'required|string',
            'merchant_id' => 'required|int',
            'status' => 'required|string',
            'refunded_at' => 'nullable|string',
            'created_at' => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function getCompanyId(): ?int
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->loan_id);

        return isset($arr[0]) ? (int) $arr[0] : null;
    }

    public function getInstallmentId(): ?int
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->loan_id);

        return isset($arr[1]) ? (int) $arr[1] : null;
    }

    public function isGuarantorPayment(): bool
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->loan_id);

        return is_array($arr) && count($arr) === 3;
    }

    public function getInstallmentGuarantorCustomerId(): ?int
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->loan_id);

        return isset($arr[2]) ? (int) $arr[2] : null;
    }
}
