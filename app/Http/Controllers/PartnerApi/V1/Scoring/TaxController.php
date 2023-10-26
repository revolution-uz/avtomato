<?php

namespace App\Http\Controllers\PartnerApi\V1\Scoring;

use App\DTOs\Scoring\Plum\Tax\TaxCreateRequestDTO;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSingleDataResponse;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Scoring\CreateTaxHistoryRequest;
use App\Http\Requests\Scoring\ScoringFilterRequest;
use App\Http\Requests\Scoring\ScoringRequestsDeleteRequest;
use App\Http\Resources\Scoring\TaxHistoryResource;
use App\Models\Company\ScoringRequest;
use App\Services\Scoring\PlumService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class TaxController extends Controller
{
    public function __construct(
        private readonly PlumService $plumService,
    ) {
    }

    #[OA\Get(
        path: '/api/v1/scoring/tax',
        description: 'Get TAX requests list',
        tags: ['Scoring (TAX info)'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'TaxHistoryResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(ScoringFilterRequest $request)
    {
        $sortables = ['id', 'status', 'created_at'];

        return TaxHistoryResource::collection(
            ScoringRequest::query()
                ->where('type', ScoringRequestTypeEnum::PLUM_TAX_INFO)
                ->when(
                    $request->search,
                    fn (Builder $q) => $q
                        ->orWhere('request->pinfl', 'ILIKE', '%' . $request->search . '%')
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
        path: '/api/v1/scoring/tax/create',
        description: 'Create TAX history request',
        requestBody: new OA\RequestBody(
            description: 'Create TAX history request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateTaxHistoryRequest')
        ),
        tags: ['Scoring (TAX info)'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'TaxHistoryResource'),
        ]
    )]
    public function create(CreateTaxHistoryRequest $request)
    {
        $dto = TaxCreateRequestDTO::from($request->validated());
        $model = $this->plumService->scoringTax($dto, ScoringRequestTypeEnum::PLUM_TAX_INFO);

        return ApiResponse::success(TaxHistoryResource::make($model));
    }

    #[OA\Delete(
        path: '/api/v1/scoring/tax',
        description: 'Tax delete route',
        requestBody: new OA\RequestBody(
            description: 'Tax delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ScoringRequestsDeleteRequest')
        ),
        tags: ['Scoring (TAX info)'],
        responses: [
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ]
    )]
    public function delete(ScoringRequestsDeleteRequest $request): JsonResponse
    {
        ScoringRequest::query()->whereIn('id', $request->ids)->delete();

        return ApiResponse::success(true);
    }
}
