<?php

namespace App\Http\Controllers\PartnerApi\V1\Scoring;

use App\DTOs\Scoring\Plum\Mib\MibInfoCreateRequestDTO;
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
use App\Http\Requests\Scoring\CreateMibInfoRequest;
use App\Http\Requests\Scoring\ScoringFilterRequest;
use App\Http\Requests\Scoring\ScoringRequestDeleteRequest;
use App\Http\Requests\Scoring\ScoringRequestViewRequest;
use App\Http\Resources\Scoring\MibInfoResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\ScoringRequest;
use App\Services\Scoring\MibService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class MibController extends Controller
{
    public function __construct(
        private readonly MibService $mibService,
    ) {}

    #[OA\Get(
        path: '/api/v1/scoring/mib',
        description: 'Get MIB requests list',
        tags: ['Scoring (MIB info)'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'MibInfoResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(ScoringFilterRequest $request)
    {
        $sortables = ['id', 'status', 'created_at'];

        return MibInfoResource::collection(
            ScoringRequest::query()
                ->where('type', ScoringRequestTypeEnum::PLUM_MIB_INFO)
                ->when($request->search, function (Builder $q) use ($request) {
                    $search = '%' . $request->search . '%';
                    $q->where(function (Builder $query) use ($search) {
                        $query->where('request->pinfl', 'ILIKE', $search)
                            ->orWhere('request->phone', 'ILIKE', $search);
                    });
                })
                ->when($request->sort && in_array($request->sort, $sortables), function (Builder $q) use ($request) {
                    if (Str::startsWith($request->sort, '-')) {
                        $q->orderBy(Str::substr($request->sort, 1), 'desc');
                    } else {
                        $q->orderBy($request->sort);
                    }
                })
                ->orderByDesc('id')
                ->paginate($request->per_page)
        )->additional(['sortables' => $sortables]);
    }

    #[OA\Post(
        path: '/api/v1/scoring/mib/create',
        description: 'Create MIB history request',
        requestBody: new OA\RequestBody(
            description: 'Create MIB history request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateMibInfoRequest')
        ),
        tags: ['Scoring (MIB info)'],
        responses: [
            new OpenApiDataResponse(resourceName: 'MibInfoResource'),
        ]
    )]
    public function create(CreateMibInfoRequest $request)
    {
        $dto = MibInfoCreateRequestDTO::from($request->validated());
        $model = $this->mibService->scoringMib($dto);

        return new MibInfoResource($model);
    }

    #[OA\Get(
        path: '/api/v1/scoring/mib/{id}',
        description: 'Get MIB info scoring request by id',
        tags: ['Scoring (MIB info)'],
        parameters: [
            new OpenApiIdParameter(modelName: 'ScoringRequest'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'MibInfoResource'),
        ]
    )]
    public function read(int $id, ScoringRequestViewRequest $request)
    {
        $scoring = ScoringRequest::findOrFail($id);
        if ($scoring->status !== ScoringRequestStatusEnum::SUCCESS || $scoring->type !== ScoringRequestTypeEnum::PLUM_MIB_INFO) {
            throw new ModelNotFoundException;
        }

        return new MibInfoResource($scoring);
    }

    #[OA\Delete(
        path: '/api/v1/scoring/mib',
        description: 'Mib delete route',
        requestBody: new OA\RequestBody(
            description: 'Mib delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ScoringRequestsDeleteRequest')
        ),
        tags: ['Scoring (MIB info)'],
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
