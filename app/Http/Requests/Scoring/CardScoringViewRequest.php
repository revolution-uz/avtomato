<?php

namespace App\Http\Requests\Scoring;

use App\DTOs\Scoring\Plum\Mib\MibCreateRequestDTO;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateCardScoringRequest',
    title: 'Request to create a request for Card scoring history',
    properties: [
        new OA\Property(property: 'number', type: 'int', example: '9860082443467251'),
        new OA\Property(property: 'expiry', type: 'int', example: '2808'),
    ],
)]
/**
 * @mixin MibCreateRequestDTO
 */
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

    public function rules(): array
    {
        return [
            'number' => 'required|integer|digits:16',
            'expiry' => 'required|int|digits:4',
        ];
    }
}
