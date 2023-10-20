<?php

namespace App\Http\Controllers\PartnerApi\V1\Scoring;

use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
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
use App\Http\Requests\Scoring\CreateKatmCreditHistoryRequest;
use App\Http\Requests\Scoring\KatmPensionInfoFilterRequest;
use App\Http\Resources\Scoring\KatmPensionInfoResource;
use App\Models\Company\ScoringRequest;
use App\Services\Scoring\KatmService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class KatmPensionInfoController extends Controller
{
    public function __construct(
        private readonly KatmService $katmService,
    ) {
    }

    #[OA\Get(
        path: '/api/v1/scoring/pension-info',
        description: 'Get pension info scoring requests list',
        tags: ['Scoring (Pension info)'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'KatmPensionInfoResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(KatmPensionInfoFilterRequest $request)
    {
        $sortables = ['id', 'status', 'created_at'];

        return KatmPensionInfoResource::collection(
            ScoringRequest::query()
                ->where('type', ScoringRequestTypeEnum::KATM_PENSION_INFO)
                ->when(
                    $request->search,
                    fn (Builder $q) => $q
                        ->orWhere('request->pinfl', 'ILIKE', '%' . $request->search . '%')
                        ->orWhere('request->first_name', 'ILIKE', '%' . $request->search . '%')
                        ->orWhere('request->last_name', 'ILIKE', '%' . $request->search . '%')
                        ->orWhere('request->middle_name', 'ILIKE', '%' . $request->search . '%')
                        ->orWhere('request->passport_series', 'ILIKE', '%' . $request->search . '%')
                        ->orWhere('request->passport_number', 'ILIKE', '%' . $request->search . '%')
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
        path: '/api/v1/scoring/pension-info/create',
        description: 'Create pension info request',
        requestBody: new OA\RequestBody(
            description: 'Create pension info request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateKatmCreditHistoryRequest')
        ),
        tags: ['Scoring (Pension info)'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'KatmPensionInfoResource'),
        ]
    )]
    public function create(CreateKatmCreditHistoryRequest $request)
    {
        $dto = KatmCreateRequestDTO::from($request->validated());
        $model = $this->katmService->createRequest($dto, ScoringRequestTypeEnum::KATM_PENSION_INFO);

        return ApiResponse::success(KatmPensionInfoResource::make($model));
    }

    #[OA\Get(
        path: '/api/v1/scoring/pension-history/{id}',
        description: 'Read single pension history request',
        tags: ['Scoring (Pension history)'],
        parameters: [
            new OpenApiIdParameter(modelName: 'ScoringRequest'),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'KatmPensionInfoResource'),
        ]
    )]
    public function read(int $id)
    {
        $scoring = ScoringRequest::findOrFail($id);
        if ($scoring->status !== ScoringRequestStatusEnum::SUCCESS || $scoring->type !== ScoringRequestTypeEnum::KATM_PENSION_INFO) {
            throw new ModelNotFoundException;
        }

        return ApiResponse::success(KatmPensionInfoResource::make($scoring));
    }
}
