<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\InstallmentCard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentCardRequest',
    title: 'Request to create or update a card',
    properties: [
        new OA\Property(property: 'card_id', type: 'int', example: '1'),
    ],
)]
/**
 * use @mixin InstallmentCard
 */
class InstallmentCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'card_id' => 'required|exists:cards,id',
        ];
    }

    public function authorize(): bool
    {
        return match (Route::currentRouteName()) {
            'installments.attach-card' => RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_ATTACH_CARD),
            'installments.detach-card' => RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_DETACH_CARD),
            default => false,
        };
    }
}
