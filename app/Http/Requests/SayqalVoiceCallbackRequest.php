<?php

namespace App\Http\Requests;

use App\Helpers\Utilities;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $dialId
 * @property string $requestId
 * @property string $callStatus
 * @property string $callMessage
 * @property Carbon $dialTime
 * @property Carbon $answerTime
 * @property string $duration
 * @property string|null $abonChoice
 */
class SayqalVoiceCallbackRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'dialId' => 'required|string',
            'requestId' => 'required|string',
            'callStatus' => 'required|string',
            'callMessage' => 'required|string',
            'dialTime' => 'required|string',
            'answerTime' => 'required|string',
            'duration' => 'required|string',
            'abonChoice' => 'nullable|string',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function getCompanyId()
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->requestId);

        return $arr[0] ?? null;
    }

    public function getRequestId()
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->requestId);

        return $arr[1] ?? null;
    }
}
