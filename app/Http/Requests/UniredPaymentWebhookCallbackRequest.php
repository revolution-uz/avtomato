<?php

namespace App\Http\Requests;

use App\Helpers\Utilities;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $pinfl
 * @property string $loan_id
 * @property string $transaction_id
 * @property string $ext_id
 * @property string $card_mask
 * @property string $card_owner
 * @property int $amount
 * @property Carbon $date
 * @property string $terminal
 * @property string $merchant
 * @property int $filial_id
 * @property string $type
 * @property string $status
 * @property Carbon|null $refunded_at
 * @property Carbon $created_at
 * @property bool|null $is_sent
 */
class UniredPaymentWebhookCallbackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pinfl' => 'required|string',
            'loan_id' => 'required|string',
            'transaction_id' => 'required|string',
            'ext_id' => 'required|string',
            'card_mask' => 'required|string',
            'card_owner' => 'required|string',
            'amount' => 'required|int',
            'date' => 'required|string',
            'terminal' => 'required|string',
            'merchant' => 'required|string',
            'filial_id' => 'required|int',
            'type' => 'required|string',
            'status' => 'required|string',
            'refunded_at' => 'nullable|string',
            'created_at' => 'required|string',
            'is_sent' => 'nullable|boolean',
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
