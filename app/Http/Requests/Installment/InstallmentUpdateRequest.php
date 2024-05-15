<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use App\Models\Company\Tariff;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentUpdateRequest',
    title: 'Request to update an installment',
    properties: [
        new OA\Property(property: 'tariff_id', type: 'int', example: '1'),
        new OA\Property(property: 'period', type: 'int', example: '12'),
        new OA\Property(property: 'amount', type: 'double', example: '2100000'),
        new OA\Property(property: 'initial_fee', type: 'double', example: '100000'),
        new OA\Property(property: 'starts_in', type: 'string', format: 'date', example: '01-12-2023'),
    ],
)]
/**
 * use @mixin Installment
 */
class InstallmentUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'tariff_id' => ['required', 'int', Rule::exists('tariffs', 'id')],
            'period' => [
                'required',
                'int',
                function ($attribute, $value, $fail) {
                    $tariff = Tariff::findOrFail($this->input('tariff_id'));
                    /** @var Tariff $tariff */
                    if ($tariff == null) {
                        $fail('Invalid tariff selected.');

                        return;
                    }
                    if ($value < $tariff->min_period || $value > $tariff->max_period) {
                        $fail("The period must be between {$tariff->min_period} and {$tariff->max_period}.");
                    }
                },
            ],
            'amount' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $tariff = Tariff::findOrFail($this->input('tariff_id'));
                    /** @var Tariff $tariff */
                    if ($tariff == null) {
                        $fail('Invalid tariff selected.');

                        return;
                    }
                    if ($value < $tariff->min_amount || $value > $tariff->max_amount) {
                        $fail("The amount must be between {$tariff->min_amount} and {$tariff->max_amount}.");
                    }
                },
            ],
            'initial_fee' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) {
                    $tariff = Tariff::findOrFail($this->input('tariff_id'));
                    /** @var Tariff $tariff */
                    if ($tariff == null) {
                        $fail('Invalid tariff selected.');

                        return;
                    }
                    if ($value < $tariff->min_initial_fee || $value > $tariff->max_initial_fee) {
                        $fail("The initial_fee must be between {$tariff->min_initial_fee} and {$tariff->max_initial_fee}.");
                    }
                },
            ],
            'starts_in' => 'required|date',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_EDIT);
    }
}
