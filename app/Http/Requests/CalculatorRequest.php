<?php

namespace App\Http\Requests;

use App\Models\Company\Tariffs;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Schema()
 *
 * @property int $tariff_id
 * @property float $amount
 * @property float $initial_amount
 * @property int $period
 */
class CalculatorRequest extends FormRequest
{

    /**
     * @OA\Property(property="phone", type="string", example="998901112233")
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'tariff_id' => 'required|exists:tariffs,id',
            'amount' => 'required|numeric',
            'initial_amount' => 'required|numeric',
            'period' => 'required|int',
        ];
    }

    /**
     * @return Builder|Tariffs|null
     */
    public function getTariff()
    {
        return Tariffs::query()->where('id', $this->tariff_id)->first();
    }

    protected function failedValidation(Validator $validator)
    {
        $response = new JsonResponse(['errors' => $validator->errors()], 422);

        throw new HttpResponseException($response);
    }
}
