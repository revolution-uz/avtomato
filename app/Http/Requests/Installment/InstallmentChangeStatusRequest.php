<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use App\Services\User\BalanceService;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

/**
 * Class InstallmentChangeStatusRequest
 *
 * @property int $status
 */
#[OA\Schema(
    schema: 'InstallmentChangeStatusRequest',
    title: 'Request to update an installment',
    properties: [
        new OA\Property(property: 'status', type: 'int', enum: InstallmentStatusEnum::class, example: '1'),
    ],
)]
class InstallmentChangeStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $installment = Installment::query()->where('id', $this->route('id'))
                        ->firstOrFail();
                    $status = InstallmentStatusEnum::tryFrom($value);
                    if (! $status) {
                        $fail('Status is not valid');
                    }

                    if (! in_array($installment->status, InstallmentStatusEnum::getStatusListByStatus($status))) {
                        $fail('Status is not valid for this installment');
                    }
                },
            ],
        ];
    }

    public function authorize(BalanceService $balanceService): bool
    {
        $permissionEnum = InstallmentStatusEnum::getPermissions($this->status);

        if (! $permissionEnum instanceof PermissionEnum) {
            return false;
        }

        return RbacHelper::checkPermission($permissionEnum) && $balanceService->isBalanceEnoughForFeature($permissionEnum->getMyFeature());
    }
}
