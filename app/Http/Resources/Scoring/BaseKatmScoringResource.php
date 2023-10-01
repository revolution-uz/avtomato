<?php

namespace App\Http\Resources\Scoring;

use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Jobs\UpdateScoringRequestStatusJob;
use App\Models\Company;
use App\Models\Company\ScoringRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * use @mixin ScoringRequest
 */
class BaseKatmScoringResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        if ($this->status === ScoringRequestStatusEnum::WAITING) {
            /** @var Company $currentCompany */
            $currentCompany = app('current_company');
            dispatch(new UpdateScoringRequestStatusJob($currentCompany->id, $this->id));
        }

        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'request' => $this->request->toArray(),
            'response' => $this->response?->toArray(),
            'created_at' => $this->created_at ? (int)$this->created_at->format('U') : null,
            'updated_at' => $this->updated_at ? (int)$this->updated_at->format('U') : null,
        ];
    }
}
