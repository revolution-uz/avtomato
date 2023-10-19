<?php

namespace App\Http\Controllers\PartnerApi\V1\Installment;

use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiJsonArrayResponse;
use App\Helpers\Swagger\OpenApiJsonResponse;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSingleDataResponse;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Helpers\ApiResponse;
use App\Http\Requests\Installment\TransactionTypeFilterRequest;
use App\Http\Requests\Installment\TransactionTypeRequest;
use App\Http\Requests\Installment\TransactionTypesDeleteRequest;
use App\Http\Resources\Installment\TransactionResource;
use App\Http\Resources\Installment\TransactionTypeResource;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class TransactionTypeController extends Controller
{
    #[OA\Get(
        path: '/api/v1/installment/transaction-type',
        description: 'TransactionType route',
        tags: ['TransactionTypes'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'TransactionTypeResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(TransactionTypeFilterRequest $request): AnonymousResourceCollection
    {
        $sortables = ['id', 'name', 'is_system']; // TODO add all fields, it's just for testing

        return TransactionTypeResource::collection(
            TransactionType::query()
                ->when(
                    $request->search,
                    fn (Builder $q) => $q->where('id', $request->search)
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

    #[OA\Get(
        path: '/api/v1/installment/transaction-type/{id}',
        description: 'TransactionType get single route',
        tags: ['TransactionTypes'],
        parameters: [
            new OpenApiIdParameter(modelName: 'TransactionType'),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'TransactionTypeResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(int $id): JsonResponse
    {
        return ApiResponse::success(TransactionTypeResource::make(Transaction::findOrFail($id)));
    }

    #[OA\Post(
        path: '/api/v1/installment/transaction-type',
        description: 'TransactionType create route',
        requestBody: new OA\RequestBody(
            description: 'TransactionType create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TransactionTypeRequest')
        ),
        tags: ['TransactionTypes'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'TransactionTypeResource'),
        ]
    )]
    public function create(TransactionTypeRequest $request): JsonResponse
    {
        $transactionType = new TransactionType;
        $transactionType->fill($request->validated());
        $transactionType->setNames($request->name);
        $transactionType->is_system = false;

        if ($transactionType->save()) {
            return ApiResponse::success(TransactionTypeResource::make($transactionType));
        }

        return ApiResponse::error('Error on creating Installment');
    }

    #[OA\Put(
        path: '/api/v1/installment/transaction-type/{id}',
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
            new OpenApiJsonResponse(resourceName: 'TransactionTypeResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function update(int $id, TransactionTypeRequest $request): JsonResponse
    {
        $transactionType = TransactionType::findOrFail($id);
        $transactionType->fill($request->validated());
        $transactionType->setNames($request->name);

        if ($transactionType->save()) {
            return ApiResponse::success(TransactionResource::make($transactionType));
        }

        return ApiResponse::error('Error on updating TransactionType');
    }

    #[OA\Delete(
        path: '/api/v1/installment/transaction-type',
        description: 'TransactionType delete route',
        requestBody: new OA\RequestBody(
            description: 'TransactionTypes delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TransactionTypesDeleteRequest')
        ),
        tags: ['TransactionTypes'],
        responses: [
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ]
    )]
    public function delete(TransactionTypesDeleteRequest $request): JsonResponse
    {
        TransactionType::query()->whereIn('id', $request->ids)->where('id', '>', 99)->delete();

        return ApiResponse::success(true);
    }
}
