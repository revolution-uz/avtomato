<?php

namespace App\Http\Controllers\PartnerApi\V1\Scoring;

use App\DTOs\Scoring\Plum\Tax\TaxInfoCreateRequestDTO;
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
use App\Http\Requests\Scoring\CreateTaxInfoRequest;
use App\Http\Requests\Scoring\ScoringFilterRequest;
use App\Http\Requests\Scoring\ScoringRequestDeleteRequest;
use App\Http\Requests\Scoring\ScoringRequestViewRequest;
use App\Http\Resources\Scoring\TaxInfoResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\ScoringRequest;
use App\Services\Scoring\TaxService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class TaxController extends Controller
{
    public function __construct(
        private readonly TaxService $taxService,
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
            new OpenApiArrayResponse(resourceName: 'TaxInfoResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(ScoringFilterRequest $request)
    {
        $sortables = ['id', 'status', 'created_at'];

        return TaxInfoResource::collection(
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
        )->additional(['sortables' => $sortables]);
    }

    #[OA\Post(
        path: '/api/v1/scoring/tax/create',
        description: 'Create TAX history request',
        requestBody: new OA\RequestBody(
            description: 'Create TAX history request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateTaxInfoRequest')
        ),
        tags: ['Scoring (TAX info)'],
        responses: [
            new OpenApiDataResponse(resourceName: 'TaxInfoResource'),
        ]
    )]
    public function create(CreateTaxInfoRequest $request)
    {
        $dto = TaxInfoCreateRequestDTO::from($request->validated());
        $model = $this->taxService->scoringTax($dto);

        return new TaxInfoResource($model);
    }

    #[OA\Get(
        path: '/api/v1/scoring/tax/{id}',
        description: 'Get TAX info scoring request by id',
        tags: ['Scoring (TAX info)'],
        parameters: [
            new OpenApiIdParameter(modelName: 'ScoringRequest'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'TaxInfoResource'),
        ]
    )]
    public function read(int $id, ScoringRequestViewRequest $request)
    {
        $scoring = ScoringRequest::findOrFail($id);
        if ($scoring->status !== ScoringRequestStatusEnum::SUCCESS || $scoring->type !== ScoringRequestTypeEnum::PLUM_TAX_INFO) {
            throw new ModelNotFoundException;
        }

        return new TaxInfoResource($scoring);
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
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function delete(ScoringRequestDeleteRequest $request): StatusResource
    {
        ScoringRequest::query()->whereIn('id', $request->ids)->delete();

        return new StatusResource(true);
    }
}
