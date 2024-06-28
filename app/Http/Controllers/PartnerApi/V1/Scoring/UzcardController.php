<?php

namespace App\Http\Controllers\PartnerApi\V1\Scoring;

use App\DTOs\Scoring\Card\CardCreateRequestDTO;
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
use App\Http\Requests\Scoring\CardScoringViewRequest;
use App\Http\Requests\Scoring\CreateCardScoringRequest;
use App\Http\Requests\Scoring\ScoringFilterRequest;
use App\Http\Requests\Scoring\ScoringRequestDeleteRequest;
use App\Http\Resources\Scoring\CardScoringResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\ScoringRequest;
use App\Services\Scoring\CardScoringService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class UzcardController extends Controller
{
    public function __construct(
        private readonly CardScoringService $scoringService,
    ) {}

    #[OA\Get(
        path: '/api/v1/scoring/uzcard',
        description: 'Get Uzcard requests list',
        tags: ['Scoring (Uzcard)'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'CardScoringResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(ScoringFilterRequest $request)
    {
        $sortables = ['id', 'status', 'created_at'];

        return CardScoringResource::collection(
            ScoringRequest::query()
                ->where('type', ScoringRequestTypeEnum::UZCARD)
                ->when($request->search, function (Builder $q) use ($request) {
                    $search = '%' . $request->search . '%';
                    $q->where(function (Builder $query) use ($search) {
                        $query->where('request->number', 'ILIKE', $search);
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
        path: '/api/v1/scoring/uzcard/create',
        description: 'Create Uzcard scoring request',
        requestBody: new OA\RequestBody(
            description: 'Create uzcard scoring request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateCardScoringRequest')
        ),
        tags: ['Scoring (Uzcard)'],
        responses: [
            new OpenApiDataResponse(resourceName: 'CardScoringResource'),
        ]
    )]
    public function create(CreateCardScoringRequest $request)
    {
        $dto = CardCreateRequestDTO::from($request->validated());
        $model = $this->scoringService->scoringUzcard($dto);

        return new CardScoringResource($model);
    }

    #[OA\Get(
        path: '/api/v1/scoring/uzcard/{id}',
        description: 'Read single Uzcard scoring request',
        tags: ['Scoring (Uzcard)'],
        parameters: [
            new OpenApiIdParameter(modelName: 'ScoringRequest'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'CardScoringResource'),
        ]
    )]
    public function read(int $id, CardScoringViewRequest $request)
    {
        $scoring = ScoringRequest::query()->where('status', ScoringRequestStatusEnum::SUCCESS)->where('type', ScoringRequestTypeEnum::UZCARD)->where('id', $id)->firstOrFail();

        return new CardScoringResource($scoring);
    }

    #[OA\Delete(
        path: '/api/v1/scoring/uzcard',
        description: 'Uzcard scoring delete route',
        requestBody: new OA\RequestBody(
            description: 'Uzcard scoring delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ScoringRequestsDeleteRequest')
        ),
        tags: ['Scoring (Uzcard)'],
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
