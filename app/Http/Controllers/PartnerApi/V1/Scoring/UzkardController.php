<?php

namespace App\Http\Controllers\PartnerApi\V1\Scoring;

use App\DTOs\Scoring\Card\CardCreateRequestDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Scoring\CreateCardScoringRequest;
use App\Http\Requests\Scoring\ScoringFilterRequest;
use App\Http\Resources\Scoring\CardScoringResource;
use App\Models\Company\ScoringRequest;
use App\Services\Scoring\CardScoringService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class UzkardController extends Controller
{
    public function __construct(
        private readonly CardScoringService $scoringService,
    ) {
    }

    #[OA\Get(
        path: '/api/v1/scoring/uzkard',
        description: 'Get Uzkard requests list',
        tags: ['Scoring (Uzkard)'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'CardScoringResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(ScoringFilterRequest $request)
    {
        $sortables = ['id', 'status', 'created_at'];

        return CardScoringResource::collection(
            ScoringRequest::query()
                ->when(
                    $request->search,
                    fn (Builder $q) => $q
                        ->orWhere('request->number', 'ILIKE', '%' . $request->search . '%')
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
        path: '/api/v1/scoring/uzkard/create',
        description: 'Create Uzkard scoring request',
        requestBody: new OA\RequestBody(
            description: 'Create Uzkard scoring request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateCardScoringRequest')
        ),
        tags: ['Scoring (Uzkard)'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'CardScoringResource'),
        ]
    )]
    public function create(CreateCardScoringRequest $request)
    {
        $dto = CardCreateRequestDTO::from($request->validated());
        $model = $this->scoringService->scoringUzkard($dto, ScoringRequestTypeEnum::UZKARD);

        return ApiResponse::success(CardScoringResource::make($model));
    }

    #[OA\Get(
        path: '/api/v1/scoring/uzkard/{id}',
        description: 'Read single Uzkard scoring request',
        tags: ['Scoring (Uzkard)'],
        parameters: [
            new OpenApiIdParameter(modelName: 'ScoringRequest'),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'CardScoringResource'),
        ]
    )]
    public function read(int $id)
    {
        $scoring = ScoringRequest::findOrFail($id);
        if ($scoring->status !== ScoringRequestStatusEnum::SUCCESS || $scoring->type !== ScoringRequestTypeEnum::UZKARD) {
            throw new ModelNotFoundException;
        }

        return ApiResponse::success(CardScoringResource::make($scoring));
    }
}
