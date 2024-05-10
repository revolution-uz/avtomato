<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Services\User\BalanceService;
use Illuminate\Foundation\Http\FormRequest;

class InstallmentStartRequest extends FormRequest
{
    public function authorize(BalanceService $balanceService): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_START) && $balanceService->isBalanceEnoughForFeature(PermissionEnum::getFeature(PermissionEnum::INSTALLMENT_START));
    }
}
