<?php

namespace App\Http\Requests\Scoring;

use App\DTOs\Scoring\Plum\Tax\TaxCreateRequestDTO;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateTaxHistoryRequest',
    title: 'Request to create a request for Tax scoring history',
    properties: [
        new OA\Property(property: 'pinfl', type: 'int', example: '51008055970017'),
    ],
)]
/**
 * @mixin TaxCreateRequestDTO
 */
class CreateTaxHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::SCORING_TAX_INFO_CREATE);
    }

    public function rules(): array
    {
        return [
            'pinfl' => 'required|integer|digits:14',
        ];
    }
}
