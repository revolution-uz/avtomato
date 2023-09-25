<?php

namespace App\Http\Requests;

use App\Enums\Company\AttachmentTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AttachmentRequest',
    title: 'Attachment upload body',
    description: 'Attachment upload body. Send as multipart/form-data',
    properties: [
        new OA\Property(property: 'file', description: 'File to upload', type: 'file'),
        new OA\Property(property: 'type', description: 'Attachment type', type: 'integer', enum: [1], example: 1),
        new OA\Property(property: 'name', description: 'Attachment name', type: 'string', example: 'My file'),
    ],
)]
class AttachmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:10240',
            'type' => 'required|int|in:' . implode(',', AttachmentTypeEnum::values()),
            'name' => 'nullable|string|max:255',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
