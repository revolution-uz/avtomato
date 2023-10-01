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
/**
 * @property Attachment $resource
 */
class AttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'type' => $this->resource->type,
            'preview_link' => $this->resource->preview_link,
            'link' => $this->resource->link,
            'name' => $this->resource->name,
            'original_name' => $this->resource->original_name,
            'mime' => $this->resource->mime,
        ];
    }
}
