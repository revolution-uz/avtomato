<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\TransactionType;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TransactionTypeRequest',
    title: 'Request to create or update a TransactionType',
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Naqd'),
    ],
)]
/**
 * @property string $name
 *                        use @mixin TransactionType
 */
class TransactionTypeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return $this->id ?
            RbacHelper::checkPermission(PermissionEnum::TRANSACTION_TYPE_EDIT) :
            RbacHelper::checkPermission(PermissionEnum::TRANSACTION_TYPE_CREATE);
    }
}
