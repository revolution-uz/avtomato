<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentDocumentUploadRequest',
    title: 'Installment Document upload body',
    description: 'Document upload body. Send as multipart/form-data',
    properties: [
        new OA\Property(property: 'name', description: 'Anketa', type: 'string'),
        new OA\Property(property: 'file', description: 'File to upload', type: 'file'),
    ],
)]
/**
 * @property string $name
 * @property file $file
 **/
class InstallmentDocumentUploadRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'file' => 'required|file|mimes:png,jpg,jpeg|max:10240',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_UPLOAD_DOCUMENT);
    }
}
