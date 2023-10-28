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

class HumoController extends Controller
{
    public function __construct(
        private readonly CardScoringService $scoringService,
    ) {
    }

    #[OA\Get(
        path: '/api/v1/scoring/humo',
        description: 'Get Humo requests list',
        tags: ['Scoring (Humo)'],
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
                ->where('type', ScoringRequestTypeEnum::HUMO)
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
        )->additional(['sortables' => $sortables]);
    }

    #[OA\Post(
        path: '/api/v1/scoring/humo/create',
        description: 'Create Humo scoring request',
        requestBody: new OA\RequestBody(
            description: 'Create Humo scoring request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateCardScoringRequest')
        ),
        tags: ['Scoring (Humo)'],
        responses: [
            new OpenApiDataResponse(resourceName: 'CardScoringResource'),
        ]
    )]
    public function create(CreateCardScoringRequest $request)
    {
        $dto = CardCreateRequestDTO::from($request->validated());
        $model = $this->scoringService->scoringHumo($dto);

        return new CardScoringResource($model);
    }

    #[OA\Get(
        path: '/api/v1/scoring/humo/{id}',
        description: 'Read single Humo scoring request',
        tags: ['Scoring (Humo)'],
        parameters: [
            new OpenApiIdParameter(modelName: 'ScoringRequest'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'CardScoringResource'),
        ]
    )]
    public function read(int $id, CardScoringViewRequest $request)
    {
        $scoring = ScoringRequest::query()->where('status', ScoringRequestStatusEnum::SUCCESS)->where('type', ScoringRequestTypeEnum::HUMO)->where('id', $id)->firstOrFail();

        return new CardScoringResource($scoring);
    }

    #[OA\Delete(
        path: '/api/v1/scoring/humo',
        description: 'Humo scoring delete route',
        requestBody: new OA\RequestBody(
            description: 'Humo scoring delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ScoringRequestsDeleteRequest')
        ),
        tags: ['Scoring (Humo)'],
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
