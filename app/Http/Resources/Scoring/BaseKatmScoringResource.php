<?php

namespace App\Http\Resources\Scoring;

use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Http\Resources\Common\DistrictResource;
use App\Http\Resources\Common\RegionResource;
use App\Jobs\UpdateScoringRequestStatusJob;
use App\Models\Company\District;
use App\Models\Company\Region;
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
            dispatch(new UpdateScoringRequestStatusJob($this->resource->id))->onQueue('scoring');
        }

        $response = [
            'id' => $this->resource->id,
            'status' => $this->resource->status->value,
            'request' => $this->resource->request->toArray(),
            'response' => $this->resource->response?->toArray(),
            'created_at' => $this->resource->created_at?->format('U'),
            'updated_at' => $this->resource->updated_at?->format('U'),
        ];

        if ($this->resource->request instanceof KatmCreateRequestDTO) {
            $response['request']['region'] = new RegionResource(Region::query()->find($response['request']['region_id']));
            $response['request']['district'] = new DistrictResource(District::query()->find($response['request']['district_id']));

        }

        return $response;
    }
}
