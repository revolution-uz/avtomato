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
use App\Http\Resources\Scoring\KatmCreditHistoryResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\ScoringRequest;
use App\Services\Scoring\KatmService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class KatmCreditHistoryController extends Controller
{
    public function __construct(
        private readonly KatmService $katmService,
    ) {
    }

    #[OA\Get(
        path: '/api/v1/scoring/credit-history',
        description: 'Get credit-history scoring requests list',
        tags: ['Scoring (Credit history)'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'KatmCreditHistoryResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(KatmCreditHistoryFilterRequest $request)
    {
        $sortables = ['id', 'status', 'created_at'];

        return KatmCreditHistoryResource::collection(
            ScoringRequest::query()
                ->where('type', ScoringRequestTypeEnum::KATM_CREDIT_HISTORY)
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
        )->additional(['sortables' => $sortables]);
    }

    #[OA\Post(
        path: '/api/v1/scoring/credit-history/create',
        description: 'Create credit history request',
        requestBody: new OA\RequestBody(
            description: 'Create credit history request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateKatmCreditHistoryRequest')
        ),
        tags: ['Scoring (Credit history)'],
        responses: [
            new OpenApiDataResponse(resourceName: 'KatmCreditHistoryResource'),
        ]
    )]
    public function create(KatmCreditHistoryCreateRequest $request)
    {
        $dto = KatmCreateRequestDTO::from($request->validated());
        $model = $this->katmService->createRequest($dto, ScoringRequestTypeEnum::KATM_CREDIT_HISTORY);

        return new KatmCreditHistoryResource($model);
    }

    #[OA\Get(
        path: '/api/v1/scoring/credit-history/{id}',
        description: 'Read single credit history request',
        tags: ['Scoring (Credit history)'],
        parameters: [
            new OpenApiIdParameter(modelName: 'ScoringRequest'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'KatmCreditHistoryResource'),
        ]
    )]
    public function read(int $id, ScoringRequestViewRequest $request)
    {
        $scoring = ScoringRequest::findOrFail($id);
        if ($scoring->status !== ScoringRequestStatusEnum::SUCCESS || $scoring->type !== ScoringRequestTypeEnum::KATM_CREDIT_HISTORY) {
            throw new ModelNotFoundException;
        }

        return new KatmCreditHistoryResource($scoring);
    }

    #[OA\Delete(
        path: '/api/v1/scoring/credit-history',
        description: 'Credit history delete route',
        requestBody: new OA\RequestBody(
            description: 'Credit history delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/ScoringRequestsDeleteRequest')
        ),
        tags: ['Scoring (Credit history)'],
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
