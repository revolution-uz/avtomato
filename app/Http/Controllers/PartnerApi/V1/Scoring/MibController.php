<?php

namespace App\Http\Controllers\PartnerApi\V1\Scoring;

use App\DTOs\Scoring\Plum\Mib\MibCreateRequestDTO;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Scoring\CreateMibHistoryRequest;
use App\Http\Requests\Scoring\ScoringFilterRequest;
use App\Http\Resources\Scoring\MibHistoryResourceKatm;
use App\Models\Company\ScoringRequest;
use App\Services\Scoring\PlumService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class MibController extends Controller
{
    public function __construct(
        private readonly PlumService $plumService,
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
            new OpenApiJsonArrayResponse(resourceName: 'MibHistoryResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(ScoringFilterRequest $request)
    {
        $sortables = ['id', 'status', 'created_at'];

        return MibHistoryResourceKatm::collection(
            ScoringRequest::query()
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
            content: new OA\JsonContent(ref: '#/components/schemas/CreateMibHistoryRequest')
        ),
        tags: ['Scoring (MIB info)'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'MibHistoryResource'),
        ]
    )]
    public function create(CreateMibHistoryRequest $request)
    {
        $dto = MibCreateRequestDTO::from($request->validated());
        $model = $this->plumService->scoringMib($dto, ScoringRequestTypeEnum::PLUM_MIB_INFO);

        return ApiResponse::success(MibHistoryResourceKatm::make($model));
    }
}
