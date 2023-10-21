<?php

namespace App\Http\Requests\Scoring;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class KatmCreditHistoryViewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return match (Route::currentRouteName()) {
            'credit-history.read' => RbacHelper::checkPermission(PermissionEnum::SCORING_CREDIT_HISTORY_VIEW),
            'inps-history.read' => RbacHelper::checkPermission(PermissionEnum::SCORING_INPS_HISTORY_VIEW),
            'pension-info.read' => RbacHelper::checkPermission(PermissionEnum::SCORING_PENSION_INFO_VIEW),
            default => false
        };
    }
}
