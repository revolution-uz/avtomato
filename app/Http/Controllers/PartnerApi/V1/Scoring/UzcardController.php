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
use App\Helpers\Swagger\OpenApiSingleDataResponse;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Scoring\CardScoringViewRequest;
use App\Http\Requests\Scoring\CreateCardScoringRequest;
use App\Http\Requests\Scoring\ScoringFilterRequest;
use App\Http\Requests\Scoring\ScoringRequestDeleteRequest;
use App\Http\Resources\Scoring\CardScoringResource;
use App\Models\Company\ScoringRequest;
use App\Services\Scoring\CardScoringService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class UzcardController extends Controller
{
    public function __construct(
        private readonly CardScoringService $scoringService,
    ) {
    }

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
            new OpenApiJsonArrayResponse(resourceName: 'CardScoringResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(ScoringFilterRequest $request)
    {
        $sortables = ['id', 'status', 'created_at'];

        return CardScoringResource::collection(
            ScoringRequest::query()
                ->where('type', ScoringRequestTypeEnum::UZCARD)
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
        path: '/api/v1/scoring/uzcard/create',
        description: 'Create Uzcard scoring request',
        requestBody: new OA\RequestBody(
            description: 'Create uzcard scoring request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateCardScoringRequest')
        ),
        tags: ['Scoring (Uzcard)'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'CardScoringResource'),
        ]
    )]
    public function create(CreateCardScoringRequest $request)
    {
        $dto = CardCreateRequestDTO::from($request->validated());
        $model = $this->scoringService->scoringUzcard($dto);

        return ApiResponse::success(CardScoringResource::make($model));
    }

    #[OA\Get(
        path: '/api/v1/scoring/uzcard/{id}',
        description: 'Read single Uzcard scoring request',
        tags: ['Scoring (Uzcard)'],
        parameters: [
            new OpenApiIdParameter(modelName: 'ScoringRequest'),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'CardScoringResource'),
        ]
    )]
    public function read(int $id, CardScoringViewRequest $request)
    {
        $scoring = ScoringRequest::query()->where('status', ScoringRequestStatusEnum::SUCCESS)->where('type', ScoringRequestTypeEnum::UZCARD)->where('id', $id)->firstOrFail();

        return ApiResponse::success(CardScoringResource::make($scoring));
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
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ]
    )]
    public function delete(ScoringRequestDeleteRequest $request): JsonResponse
    {
        ScoringRequest::query()->whereIn('id', $request->ids)->delete();

        return ApiResponse::success(true);
    }
}
