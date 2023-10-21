<?php

namespace App\Http\Requests\Scoring;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Http\Requests\BaseFilterRequest;
use Illuminate\Support\Facades\Route;

class KatmCreditHistoryFilterRequest extends BaseFilterRequest
{
    public function authorize(): bool
    {
        return match (Route::currentRouteName()) {
            'credit-history.index' => RbacHelper::checkPermission(PermissionEnum::SCORING_CREDIT_HISTORY_VIEW_ALL),
            'inps-history.index' => RbacHelper::checkPermission(PermissionEnum::SCORING_INPS_HISTORY_VIEW_ALL),
            'pension-info.index' => RbacHelper::checkPermission(PermissionEnum::SCORING_PENSION_INFO_VIEW_ALL),
            default => false
        };
    }
}
