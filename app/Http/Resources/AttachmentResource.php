<?php

namespace App\Http\Resources;

use App\Models\Company\Attachment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AttachmentResource',
    title: 'Attachment resource',
    description: 'Attachment resource',
    properties: [
        new OA\Property(property: 'id', description: 'Attachment id', type: 'string', example: '1'),
        new OA\Property(property: 'type', description: 'Attachment type', type: 'integer', example: 1),
        new OA\Property(property: 'preview_path', description: 'Attachment preview path', type: 'string', example: '/uploads/preview/1.jpg'),
        new OA\Property(property: 'path', description: 'Attachment path', type: 'string', example: '/uploads/1.jpg'),
        new OA\Property(property: 'name', description: 'Attachment name', type: 'string', example: 'My file'),
        new OA\Property(property: 'original_name', description: 'Attachment original name', type: 'string', example: 'My file'),
        new OA\Property(property: 'mime', description: 'Attachment mime', type: 'string', example: 'image/jpeg'),
    ],
)]
/** @mixin Attachment */
class AttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'preview_link' => $this->preview_link,
            'link' => $this->link,
            'name' => $this->name,
            'original_name' => $this->original_name,
            'mime' => $this->mime,
        ];
    }
}
