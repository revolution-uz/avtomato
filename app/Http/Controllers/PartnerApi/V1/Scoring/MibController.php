<?php

namespace App\Http\Controllers\PartnerApi\V1\Scoring;

use App\DTOs\Scoring\Plum\Mib\MibInfoCreateRequestDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSingleDataResponse;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Scoring\CreateMibInfoRequest;
use App\Http\Requests\Scoring\ScoringFilterRequest;
use App\Http\Requests\Scoring\ScoringRequestDeleteRequest;
use App\Http\Requests\Scoring\ScoringRequestViewRequest;
use App\Http\Resources\Scoring\MibInfoResource;
use App\Models\Company\ScoringRequest;
use App\Services\Scoring\MibService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class MibController extends Controller
{
    public function __construct(
        private readonly MibService $mibService,
    ) {
    }

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
            new OpenApiJsonArrayResponse(resourceName: 'MibInfoResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(ScoringFilterRequest $request)
    {
        $sortables = ['id', 'status', 'created_at'];

        return MibInfoResource::collection(
            ScoringRequest::query()
                ->where('type', ScoringRequestTypeEnum::PLUM_MIB_INFO)
                ->when(
                    $request->search,
                    fn (Builder $q) => $q
                        ->orWhere('request->pinfl', 'ILIKE', '%' . $request->search . '%')
                        ->orWhere('request->phone', 'ILIKE', '%' . $request->search . '%')
                )
                ->when(
                    $request->sort && in_array($request->sort, $sortables),
                    fn (Builder $q) => Str::startsWith($request->sort, '-')
                        ? $q->orderBy(Str::substr($request->sort, 1), 'desc')
                        : $q->orderBy($request->sort)
                )
                ->paginate($request->per_page)
        )->additional(['success' => true, 'sortables' => $sortables]);
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
            new OpenApiJsonResponse(resourceName: 'MibInfoResource'),
        ]
    )]
    public function create(CreateMibInfoRequest $request)
    {
        $dto = MibInfoCreateRequestDTO::from($request->validated());
        $model = $this->mibService->scoringMib($dto);

        return ApiResponse::success(MibInfoResource::make($model));
    }

    #[OA\Get(
        path: '/api/v1/scoring/mib/{id}',
        description: 'Get MIB info scoring request by id',
        tags: ['Scoring (MIB info)'],
        parameters: [
            new OpenApiIdParameter(modelName: 'ScoringRequest'),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'MibInfoResource'),
        ]
    )]
    public function read(int $id, ScoringRequestViewRequest $request)
    {
        $scoring = ScoringRequest::findOrFail($id);
        if ($scoring->status !== ScoringRequestStatusEnum::SUCCESS || $scoring->type !== ScoringRequestTypeEnum::PLUM_MIB_INFO) {
            throw new ModelNotFoundException;
        }

        return ApiResponse::success(MibInfoResource::make($scoring));
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
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ]
    )]
    public function delete(ScoringRequestDeleteRequest $request): JsonResponse
    {
        ScoringRequest::query()->whereIn('id', $request->ids)->delete();

        return ApiResponse::success(true);
    }
}
