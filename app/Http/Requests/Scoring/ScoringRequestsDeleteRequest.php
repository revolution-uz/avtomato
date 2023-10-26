<?php

namespace App\Http\Requests\Scoring;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ScoringRequestsDeleteRequest',
    title: 'Request to delete scoring request',
    properties: [
        new OA\Property(property: 'ids', type: 'array', items: new OA\Items(type: 'int'), example: '[1, 2, 3]'),
    ],
)]
/**
 * @property int[] $ids
 */
class ScoringRequestsDeleteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => 'array',
            'ids.*' => 'integer|exists:scoring_requests,id',
        ];
    }

    public function authorize(): bool
    {
        return match (Route::currentRouteName()) {
            'credit-history.delete' => RbacHelper::checkPermission(PermissionEnum::SCORING_CREDIT_HISTORY_DELETE),
            'inps-history.delete' => RbacHelper::checkPermission(PermissionEnum::SCORING_INPS_HISTORY_DELETE),
            'pension-info.delete' => RbacHelper::checkPermission(PermissionEnum::SCORING_PENSION_INFO_DELETE),
            'mib.delete' => RbacHelper::checkPermission(PermissionEnum::SCORING_MIB_INFO_DELETE),
            'tax.delete' => RbacHelper::checkPermission(PermissionEnum::SCORING_TAX_INFO_DELETE),
            'humo.delete' => RbacHelper::checkPermission(PermissionEnum::SCORING_HUMO_DELETE),
            'uzcard.delete' => RbacHelper::checkPermission(PermissionEnum::SCORING_UZCARD_CREATE),
            default => false
        };
    }
}
