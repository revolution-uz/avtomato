<?php

namespace App\Http\Resources\Installment;

use App\Models\Company\InstallmentComment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentCommentListResource',
    title: 'InstallmentCommentListResource object',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'installment_id', type: 'int', example: '1'),
        new OA\Property(property: 'creator_id', type: 'int', example: '1'),
        new OA\Property(property: 'creator_name', type: 'string', example: 'Abrorjon Sobirov'),
        new OA\Property(property: 'comment', type: 'string', example: "Qarzini 10 kun ichida to'lashga va'da berdi"),
    ],
)]
/**
 * @property InstallmentComment $resource
 */
class InstallmentCommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'installment_id' => $this->resource->installment_id,
            'creator_id' => $this->resource->user_id,
            'creator_name' => $this->resource->user->full_name,
            'comment' => $this->resource->comment,
            'created_at' => $this->resource->created_at?->format('U'),
            'updated_at' => $this->resource->updated_at?->format('U'),
        ];
    }
}
