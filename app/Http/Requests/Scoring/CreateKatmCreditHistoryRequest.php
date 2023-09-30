<?php

namespace App\Http\Requests\Scoring;

use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateKatmCreditHistoryRequest',
    title: 'Request to create a request for KATM credit history',
    properties: [
        new OA\Property(property: 'pinfl', type: 'int', example: '51008055970017'),
        new OA\Property(property: 'passport_series', type: 'string', example: 'AD'),
        new OA\Property(property: 'passport_number', type: 'string', example: '0988302'),
        new OA\Property(property: 'passport_given_date', type: 'string', format: 'date', example: '12-01-2022'),
        new OA\Property(property: 'first_name', type: 'string', example: 'Azizbek'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Sobitov'),
        new OA\Property(property: 'middle_name', type: 'string', example: 'Sobitxon o\'g\'li', nullable: true),
        new OA\Property(property: 'phone', type: 'int', example: '998972538005'),
        new OA\Property(property: 'extra_phone', type: 'int', example: '998972538005', nullable: true),
        new OA\Property(property: 'birth_date', type: 'string', format: 'date', example: '10-05-2005'),
        new OA\Property(property: 'region_id', type: 'int', example: '1', nullable: true),
        new OA\Property(property: 'district_id', type: 'int', example: '2', nullable: true),
    ],
)]
/**
 * @mixin KatmCreateRequestDTO
 */
class CreateKatmCreditHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pinfl' => 'required|integer|digits:14',
            'passport_series' => 'required|string|max:2|min:2',
            'passport_number' => 'required|string|max:7|min:7|regex:/^[0-9]+$/',
            'passport_given_date' => 'required|date',
            'first_name' => 'required|string|max:32',
            'last_name' => 'required|string|max:32',
            'middle_name' => 'nullable|string|max:32',
            'phone' => 'required|int|digits:12',
            'extra_phone' => 'required|int|digits:12',
            'birth_date' => 'required|date',
            'region_id' => 'required|int|exists:regions,id',
            'district_id' => 'required|int|exists:districts,id',
        ];
    }
}
