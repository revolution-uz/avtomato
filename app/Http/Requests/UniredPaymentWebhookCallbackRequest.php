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
            'ext' => 'required|string',
            'card' => 'required|array',
            'card.pan' => 'nullable|string',
            'card.owner' => 'nullable|string',
            'card.phone' => 'nullable|string',
            'date' => 'required|string',
            'pinfl' => 'required|string',
            'amount' => 'required|int',
            'status' => 'required|string',
            'loan_id' => 'required|string',
            'merchant' => 'required|string',
            'terminal' => 'required|string',
            'created_at' => 'required|string',
            'merchant_id' => 'required|int',
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
