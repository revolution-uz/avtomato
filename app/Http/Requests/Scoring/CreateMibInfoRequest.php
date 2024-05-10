<?php

namespace App\Http\Requests\Scoring;

use App\DTOs\Scoring\Plum\Mib\MibInfoCreateRequestDTO;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Services\User\BalanceService;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateMibInfoRequest',
    title: 'Request to create a request for Mib scoring history',
    properties: [
        new OA\Property(property: 'pinfl', type: 'int', example: '51008055970017'),
        new OA\Property(property: 'phone', type: 'int', example: '998972538005'),
    ],
)]
/**
 * @mixin MibInfoCreateRequestDTO
 */
class CreateMibInfoRequest extends FormRequest
{
    public function authorize(BalanceService $balanceService): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::SCORING_MIB_INFO_CREATE)
            && $balanceService->isBalanceEnoughForFeature(PermissionEnum::SCORING_MIB_INFO_CREATE->getMyFeature());
    }

    public function rules(): array
    {
        return [
            'pinfl' => 'required|integer|digits:14',
            'phone' => 'required|int|digits:12',
        ];
    }
}
