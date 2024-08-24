<?php

namespace App\Http\Requests;

use App\Helpers\Utilities;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $transactionid
 * @property string $smsid
 * @property int $status
 * @property string $description
 * @property Carbon $statusdate
 * @property string $partinfo
 */
class SayqalSmsCallbackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'transactionid' => 'required|string',
            'smsid' => 'required|string',
            'status' => 'required|int',
            'description' => 'required|string',
            'statusdate' => 'required|string',
            'partinfo' => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function getCompanyId()
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->smsid);

        return $arr[0] ?? null;
    }

    public function getSmsId()
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->smsid);

        return $arr[1] ?? null;
    }

    public function smsCount()
    {
        $arr = explode(':', $this->partinfo);

        return $arr[0] ?? 1;
    }
}
