<?php

namespace App\Http\Controllers\PartnerApi\V1\Scoring;

use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
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
use App\Http\Requests\Scoring\KatmCreditHistoryCreateRequest;
use App\Http\Requests\Scoring\KatmCreditHistoryFilterRequest;
use App\Http\Requests\Scoring\ScoringRequestDeleteRequest;
use App\Http\Requests\Scoring\ScoringRequestViewRequest;
use App\Http\Resources\Scoring\KatmPensionInfoResource;
use App\Http\Resources\StatusResource;
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
            new OpenApiArrayResponse(resourceName: 'KatmPensionInfoResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(KatmCreditHistoryFilterRequest $request)
    {
        $sortables = ['id', 'status', 'created_at'];

        return KatmPensionInfoResource::collection(
            ScoringRequest::query()
                ->where('type', ScoringRequestTypeEnum::KATM_PENSION_INFO)
                ->when($request->search, function (Builder $q) use ($request) {
                    $search = '%' . $request->search . '%';
                    $q->where(function (Builder $query) use ($search) {
                        $query->where('request->pinfl', 'ILIKE', $search)
                            ->orWhere('request->first_name', 'ILIKE', $search)
                            ->orWhere('request->last_name', 'ILIKE', $search)
                            ->orWhere('request->middle_name', 'ILIKE', $search)
                            ->orWhere('request->passport_series', 'ILIKE', $search)
                            ->orWhere('request->passport_number', 'ILIKE', $search);
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
        path: '/api/v1/scoring/pension-info/create',
        description: 'Create pension info request',
        requestBody: new OA\RequestBody(
            description: 'Create pension info request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateKatmCreditHistoryRequest')
        ),
        tags: ['Scoring (Pension info)'],
        responses: [
            new OpenApiDataResponse(resourceName: 'KatmPensionInfoResource'),
        ]
    )]
    public function create(KatmCreditHistoryCreateRequest $request)
    {
        $dto = KatmCreateRequestDTO::from($request->validated());
        $model = $this->katmService->createRequest($dto, ScoringRequestTypeEnum::KATM_PENSION_INFO);

        return new KatmPensionInfoResource($model);
    }

    #[OA\Get(
        path: '/api/v1/scoring/pension-info/{id}',
        description: 'Get pension info scoring request by id',
        tags: ['Scoring (Pension info)'],
        parameters: [
            new OpenApiIdParameter(modelName: 'ScoringRequest'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'KatmPensionInfoResource'),
        ]
    )]
    public function read(int $id, ScoringRequestViewRequest $request)
    {
        $scoring = ScoringRequest::findOrFail($id);
        if ($scoring->status !== ScoringRequestStatusEnum::SUCCESS || $scoring->type !== ScoringRequestTypeEnum::KATM_PENSION_INFO) {
            throw new ModelNotFoundException;
        }

        return new KatmPensionInfoResource($scoring);
    }

    #[OA\Delete(
        path: '/api/v1/scoring/pension-info',
        description: 'Pension info history delete route',
        requestBody: new OA\RequestBody(
            description: 'Pension info delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ScoringRequestsDeleteRequest')
        ),
        tags: ['Scoring (Pension info)'],
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
