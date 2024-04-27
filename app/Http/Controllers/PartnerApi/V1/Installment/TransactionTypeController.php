<?php

namespace App\Http\Controllers\PartnerApi\V1\Installment;

use App\Helpers\Swagger\OpenApiArrayResponse;
use App\Helpers\Swagger\OpenApiDataResponse;
use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Installment\TransactionTypeFilterRequest;
use App\Http\Requests\Installment\TransactionTypeRequest;
use App\Http\Requests\Installment\TransactionTypesDeleteRequest;
use App\Http\Requests\Installment\TransactionTypeViewRequest;
use App\Http\Resources\Installment\TransactionResource;
use App\Http\Resources\Installment\TransactionTypeResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class TransactionTypeController extends Controller
{
    #[OA\Get(
        path: '/api/v1/transaction-type',
        description: 'TransactionType route',
        tags: ['TransactionTypes'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'TransactionTypeResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(TransactionTypeFilterRequest $request): AnonymousResourceCollection
    {
        $sortables = ['id', 'name', 'is_system']; // TODO add all fields, it's just for testing

        return TransactionTypeResource::collection(
            TransactionType::query()
                ->when(
                    $request->search,
                    fn (Builder $q) => $q->when(
                        filter_var($request->search, FILTER_VALIDATE_INT),
                        fn (Builder $q) => $q->where('id', $request->search)
                    )
                        ->orWhere('name_uz', 'ilike', "%{$request->search}%")
                        ->orWhere('name_ru', 'ilike', "%{$request->search}%")
                        ->orWhere('name_en', 'ilike', "%{$request->search}%")
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

    #[OA\Get(
        path: '/api/v1/transaction-type/{id}',
        description: 'TransactionType get single route',
        tags: ['TransactionTypes'],
        parameters: [
            new OpenApiIdParameter(modelName: 'TransactionType'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'TransactionTypeResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(int $id, TransactionTypeViewRequest $request): TransactionTypeResource
    {
        return new TransactionTypeResource(TransactionType::findOrFail($id));
    }

    #[OA\Post(
        path: '/api/v1/transaction-type',
        description: 'TransactionType create route',
        requestBody: new OA\RequestBody(
            description: 'TransactionType create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TransactionTypeRequest')
        ),
        tags: ['TransactionTypes'],
        responses: [
            new OpenApiDataResponse(resourceName: 'TransactionTypeResource'),
        ]
    )]
    public function create(TransactionTypeRequest $request): TransactionTypeResource
    {
        $transactionType = new TransactionType;
        $transactionType->fill($request->validated());
        $transactionType->setNames($request->name);
        $transactionType->is_system = false;
        $transactionType->saveOrFail();

        return new TransactionTypeResource($transactionType);
    }

    #[OA\Put(
        path: '/api/v1/transaction-type/{id}',
        description: 'TransactionType update route',
        requestBody: new OA\RequestBody(
            description: 'TransactionType update request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TransactionTypeRequest')
        ),
        tags: ['TransactionTypes'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'TransactionType ID',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'TransactionTypeResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function update(int $id, TransactionTypeRequest $request): TransactionResource
    {
        if ($id < 100) {
            abort(403, 'You can not edit system transaction types');
        }

        /** @var TransactionType $transactionType */
        $transactionType = TransactionType::findOrFail($id);
        $transactionType->fill($request->validated());
        $transactionType->setNames($request->name);
        $transactionType->saveOrFail();

        return new TransactionResource($transactionType);
    }

    #[OA\Delete(
        path: '/api/v1/transaction-type',
        description: 'TransactionType delete route',
        requestBody: new OA\RequestBody(
            description: 'TransactionTypes delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TransactionTypesDeleteRequest')
        ),
        tags: ['TransactionTypes'],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function delete(TransactionTypesDeleteRequest $request): StatusResource
    {
        TransactionType::query()->whereIn('id', $request->ids)->where('id', '>', 99)->delete();

        return new StatusResource(true);
    }
}
