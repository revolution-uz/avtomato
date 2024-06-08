<?php

namespace App\Http\Controllers\PartnerApi\V1\Scoring;

use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Helpers\Swagger\OpenApiArrayResponse;
use App\Helpers\Swagger\OpenApiDataResponse;
use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Scoring\KatmCreditHistoryCreateRequest;
use App\Http\Requests\Scoring\KatmCreditHistoryFilterRequest;
use App\Http\Requests\Scoring\ScoringRequestDeleteRequest;
use App\Http\Requests\Scoring\ScoringRequestViewRequest;
use App\Http\Resources\Scoring\KatmInpsHistoryResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\ScoringRequest;
use App\Services\Scoring\KatmService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use OpenApi\Attributes as OA;

class KatmInpsHistoryController extends Controller
{
    public function __construct(
        private readonly KatmService $katmService,
    ) {
    }

    #[OA\Get(
        path: '/api/v1/scoring/inps-history',
        description: 'Get INPS-history scoring requests list',
        tags: ['Scoring (INPS history)'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'KatmInpsHistoryResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(KatmCreditHistoryFilterRequest $request)
    {
        $request->sortables = ['id', 'status', 'created_at'];

        return KatmInpsHistoryResource::collection(
            ScoringRequest::query()
                ->where('type', ScoringRequestTypeEnum::KATM_INPS_HISTORY)
                ->when($request->search, function (Builder $q) use ($request) {
                    $search = '%' . $request->search . '%';
                    $q->where(function (Builder $query) use ($search) {
                        $query->where('request->pinfl', 'ILIKE', $search)
                            ->orWhere('request->first_name', 'ILIKE', $search)
                            ->orWhere('request->last_name', 'ILIKE', $search)
                            ->orWhere('request->middle_name', 'ILIKE', $search)
                            ->orWhere('request->passport_series', 'ILIKE', $search)
                            ->orWhere('request->passport_number', 'ILIKE', $search);
                    });
                })
                ->when($request->isSortable(), function (Builder $q) use ($request) {
                    $q->orderBy($request->sortField(), $request->sortDirection());
                })
                ->orderByDesc('id')
                ->paginate($request->per_page)
        )->additional(['sortables' => $request->sortables]);
    }

    #[OA\Post(
        path: '/api/v1/scoring/inps-history/create',
        description: 'Create INPS history request',
        requestBody: new OA\RequestBody(
            description: 'Create INPS history request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateKatmCreditHistoryRequest')
        ),
        tags: ['Scoring (INPS history)'],
        responses: [
            new OpenApiDataResponse(resourceName: 'KatmInpsHistoryResource'),
        ]
    )]
    public function create(KatmCreditHistoryCreateRequest $request)
    {
        $dto = KatmCreateRequestDTO::from($request->validated());
        $model = $this->katmService->createRequest($dto, ScoringRequestTypeEnum::KATM_INPS_HISTORY);

        return new KatmInpsHistoryResource($model);
    }

    #[OA\Get(
        path: '/api/v1/scoring/inps-history/{id}',
        description: 'Read single credit history request',
        tags: ['Scoring (INPS history)'],
        parameters: [
            new OpenApiIdParameter(modelName: 'ScoringRequest'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'KatmInpsHistoryResource'),
        ]
    )]
    public function read(int $id, ScoringRequestViewRequest $request)
    {
        $scoring = ScoringRequest::findOrFail($id);
        if ($scoring->status !== ScoringRequestStatusEnum::SUCCESS || $scoring->type !== ScoringRequestTypeEnum::KATM_INPS_HISTORY) {
            throw new ModelNotFoundException;
        }

        return new KatmInpsHistoryResource($scoring);
    }

    #[OA\Delete(
        path: '/api/v1/scoring/inps-history',
        description: 'INPS history delete route',
        requestBody: new OA\RequestBody(
            description: 'INPS history delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ScoringRequestsDeleteRequest')
        ),
        tags: ['Scoring (INPS history)'],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function delete(ScoringRequestDeleteRequest $request): StatusResource
    {
        ScoringRequest::query()->whereIn('id', $request->ids)->delete();

        return new StatusResource(true);
    }
}
