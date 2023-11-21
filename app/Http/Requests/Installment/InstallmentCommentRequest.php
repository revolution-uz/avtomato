<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\InstallmentGuarantor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentCommentRequest',
    title: 'Request to create or update installment comment',
    properties: [
        new OA\Property(property: 'comment', type: 'string', example: "Qarzini 10 kun ichida to'lashga va'da berdi"),
    ],
)]
/**
 * use @mixin InstallmentGuarantor
 */
class InstallmentCommentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'comment' => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return match (Route::currentRouteName()) {
            'installments.create-comment' => RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_CREATE_COMMENT),
            'installments.update-comment' => RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_UPDATE_COMMENT),
            default => false
        };
    }
}
