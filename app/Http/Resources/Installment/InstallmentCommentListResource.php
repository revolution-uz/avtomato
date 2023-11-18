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
        new OA\Property(property: 'installment_id', type: 'int', example: '1'),
        new OA\Property(property: 'user_id', type: 'int', example: '1'),
        new OA\Property(property: 'user', ref: '#/components/schemas/UserListResource'),
        new OA\Property(property: 'comment', type: 'string', example: "Qarzini 10 kun ichida to'lashga va'da berdi"),
    ],
)]
/**
 * @property InstallmentComment $resource
 */
class InstallmentCommentListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'installment_id' => $this->resource->installment_id,
            'user_id' => $this->resource->user_id,
            'user' => $this->resource->user,
            'comment' => $this->resource->comment,
            'created_at' => $this->resource->created_at->getTimestamp(),
            'updated_at' => $this->resource->updated_at->getTimestamp(),
        ];
    }
}
