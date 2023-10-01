<?php

namespace App\Http\Resources\Scoring;

use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Jobs\UpdateScoringRequestStatusJob;
use App\Models\Company\ScoringRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property ScoringRequest $resource
 */
class BaseKatmScoringResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if ($this->resource->status === ScoringRequestStatusEnum::WAITING) {
            dispatch(new UpdateScoringRequestStatusJob($this->resource->id));
        }

        return [
            'id' => $this->resource->id,
            'status' => $this->resource->status->value,
            'request' => $this->resource->request->toArray(),
            'response' => $this->resource->response?->toArray(),
            'created_at' => $this->resource->created_at ? (int) $this->resource->created_at->format('U') : null,
            'updated_at' => $this->resource->updated_at ? (int) $this->resource->updated_at->format('U') : null,
        ];
    }
}
