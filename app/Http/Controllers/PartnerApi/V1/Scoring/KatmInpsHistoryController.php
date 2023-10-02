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
use App\Http\Requests\Management\BranchesFilterRequest;
use App\Http\Requests\Scoring\CreateKatmCreditHistoryRequest;
use App\Http\Resources\Scoring\KatmInpsHistoryResource;
use App\Models\Company\ScoringRequest;
use App\Services\Scoring\KatmService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
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
            new OpenApiJsonArrayResponse(resourceName: 'KatmInpsHistoryResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(BranchesFilterRequest $request)
    {
        $sortables = ['id', 'status', 'created_at'];

        return KatmInpsHistoryResource::collection(
            ScoringRequest::query()
                ->where('type', ScoringRequestTypeEnum::KATM_INPS_HISTORY)
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
        path: '/api/v1/scoring/inps-history/create',
        description: 'Create INPS history request',
        requestBody: new OA\RequestBody(
            description: 'Create INPS history request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateKatmCreditHistoryRequest')
        ),
        tags: ['Scoring (INPS history)'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'KatmInpsHistoryResource'),
        ]
    )]
    public function create(CreateKatmCreditHistoryRequest $request)
    {
        $dto = KatmCreateRequestDTO::from($request->validated());
        $model = $this->katmService->createRequest($dto, ScoringRequestTypeEnum::KATM_INPS_HISTORY);

        return ApiResponse::success(KatmInpsHistoryResource::make($model));
    }

    #[OA\Get(
        path: '/api/v1/scoring/inps-history/{id}',
        description: 'Read single credit history request',
        tags: ['Scoring (INPS history)'],
        parameters: [
            new OpenApiIdParameter(modelName: 'ScoringRequest'),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'KatmInpsHistoryResource'),
        ]
    )]
    public function read(ScoringRequest $scoring)
    {
        if ($scoring->status !== ScoringRequestStatusEnum::SUCCESS || $scoring->type !== ScoringRequestTypeEnum::KATM_INPS_HISTORY) {
            throw new ModelNotFoundException;
        }

        return ApiResponse::success(KatmInpsHistoryResource::make($scoring));
    }
}
