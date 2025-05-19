<?php

namespace App\Http\Requests;

use App\Exceptions\GeneralException;
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
    /** @var string[] */
    private array $arr;

    public function rules(): array
    {
        return [
            'transactionid' => 'required|string',
            'smsid' => 'required|string',
            'status' => 'required|int',
            'description' => 'required|string',
            'statusdate' => 'required|string|date_format:Y-m-d H:i:s',
            'partinfo' => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function getCompanyId()
    {
        $arr = $this->getArr();

        return $arr[0] ?? null;
    }

    public function getSmsId()
    {
        $arr = $this->getArr();

        return $arr[1] ?? null;
    }

    public function smsCount()
    {
        $arr = explode(':', $this->partinfo);

        return $arr[0] ?? 1;
    }

    private function getArr()
    {
        if (! isset($this->arr)) {
            if (! preg_match('/^\d+' . Utilities::TENANT_SEPARATOR . '\d+$/', $this->smsid)) {
                throw new GeneralException('Invalid SMS ID format');
            }
            $this->arr = explode(Utilities::TENANT_SEPARATOR, $this->smsid);
        }

        return $this->arr;
    }
}
