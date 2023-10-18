<?php

namespace App\Http\Requests\Scoring;

use App\DTOs\Scoring\Plum\Mib\MibCreateRequestDTO;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateMibHistoryRequest',
    title: 'Request to create a request for Mib scoring history',
    properties: [
        new OA\Property(property: 'pinfl', type: 'int', example: '51008055970017'),
        new OA\Property(property: 'phone', type: 'int', example: '998972538005'),
    ],
)]
/**
 * @mixin MibCreateRequestDTO
 */
class CreateMibHistoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pinfl' => 'required|integer|digits:14',
            'phone' => 'required|int|digits:12',
        ];
    }
}
