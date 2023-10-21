<?php

namespace App\Http\Requests\Scoring;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class CardScoringViewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return match (Route::currentRouteName()) {
            'humo.read' => RbacHelper::checkPermission(PermissionEnum::SCORING_HUMO_VIEW),
            'uzcard.read' => RbacHelper::checkPermission(PermissionEnum::SCORING_UZCARD_VIEW),
            default => false
        };
    }
}
