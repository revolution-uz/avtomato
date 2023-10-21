<?php

namespace App\Http\Requests\Scoring;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Http\Requests\BaseFilterRequest;
use Illuminate\Support\Facades\Route;

/**
 * @property string $sort
 * @property string $search
 * @property int $page
 * @property int $per_page
 */
class ScoringFilterRequest extends BaseFilterRequest
{
    public function authorize(): bool
    {
        return match (Route::currentRouteName()) {
            'mib.index' => RbacHelper::checkPermission(PermissionEnum::SCORING_MIB_INFO_VIEW_ALL),
            'tax.index' => RbacHelper::checkPermission(PermissionEnum::SCORING_TAX_INFO_VIEW_ALL),
            'humo.index' => RbacHelper::checkPermission(PermissionEnum::SCORING_HUMO_VIEW_ALL),
            'uzcard.index' => RbacHelper::checkPermission(PermissionEnum::SCORING_UZCARD_VIEW_ALL),
            default => false
        };
    }
}
