<?php

namespace App\Http\Requests\Management;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'FundCustomerBalanceRequest',
    title: 'Request to fund balance in installment',
    properties: [
        new OA\Property(property: 'amount', type: 'integer', example: '1000'),
        new OA\Property(property: 'cardNumber', type: 'string', example: '8600312912345678'),
        new OA\Property(property: 'expireDate', type: 'string', example: '2407'),
        new OA\Property(property: 'transactionData', type: 'string', example: 'Для абон. плата за февраль 2024 года'),
    ],
)]
/**
 * @property int $amount
 * @property string cardNumber
 * @property string expireDate
 * @property string $transactionData
 */
class FundCustomerBalanceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'amount' => 'required',
            'cardNumber' => 'required|string',
            'expireDate' => 'required|string',
            'transactionData' => 'string',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_CARD_EDIT); // o'zgartirish kerak so'rab
    }
}
