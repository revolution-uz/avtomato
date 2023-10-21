<?php

namespace App\Http\Controllers\PartnerApi\V1\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
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
use App\Http\Requests\Installment\TransactionFilterRequest;
use App\Http\Requests\Installment\TransactionRequest;
use App\Http\Requests\Installment\TransactionsDeleteRequest;
use App\Http\Requests\Installment\TransactionViewRequest;
use App\Http\Resources\Installment\TransactionResource;
use App\Models\Company\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class TransactionController extends Controller
{
    #[OA\Get(
        path: '/api/v1/installment/transaction',
        description: 'Transaction route',
        tags: ['Transactions'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
        ],
        responses: [
            new OpenApiJsonArrayResponse(resourceName: 'TransactionResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(TransactionFilterRequest $request): AnonymousResourceCollection
    {
        $sortables = ['id', 'installment_id', 'customer_id', 'card_id', 'transaction_type_id', 'amount', 'on_created']; // TODO add all fields, it's just for testing
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::TRANSACTION_VIEW_ALL);

        return TransactionResource::collection(
            Transaction::query()
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
                ->when($branches,
                    fn (Builder $q) => $q->whereHas('installment',
                        fn (Builder $q) => $q->whereIn('branch_id', $branches)
                    ))
                ->paginate($request->per_page)
        )->additional(['success' => true, 'sortables' => $sortables]);
    }

    #[OA\Get(
        path: '/api/v1/installment/transaction/{id}',
        description: 'Transaction get single route',
        tags: ['Transactions'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Transaction'),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'TransactionResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(int $id, TransactionViewRequest $request): JsonResponse
    {
        return ApiResponse::success(TransactionResource::make(Transaction::findOrFail($id)));
    }

    #[OA\Post(
        path: '/api/v1/installment/transaction',
        description: 'Transaction create route',
        requestBody: new OA\RequestBody(
            description: 'Transaction create request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TransactionRequest')
        ),
        tags: ['Transactions'],
        responses: [
            new OpenApiJsonResponse(resourceName: 'TransactionResource'),
        ]
    )]
    public function create(TransactionRequest $request): JsonResponse
    {
        $model = new Transaction;
        $model->fill($request->validated());

        if ($model->save()) {
            return ApiResponse::success(TransactionResource::make($model));
        }

        return ApiResponse::error('Error on creating Installment');
    }

    #[OA\Put(
        path: '/api/v1/installment/transaction/{id}',
        description: 'Transaction update route',
        requestBody: new OA\RequestBody(
            description: 'Transaction update request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TransactionRequest')
        ),
        tags: ['Transactions'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Transaction ID',
                in: 'path',
                required: true
            ),
        ],
        responses: [
            new OpenApiJsonResponse(resourceName: 'TransactionResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function update(int $id, TransactionRequest $request): JsonResponse
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->fill($request->validated());

        if ($transaction->save()) {
            return ApiResponse::success(TransactionResource::make($transaction));
        }

        return ApiResponse::error('Error on updating Transaction');
    }

    #[OA\Delete(
        path: '/api/v1/installment/transaction',
        description: 'Transaction delete route',
        requestBody: new OA\RequestBody(
            description: 'Transactions delete request body',
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/TransactionsDeleteRequest')
        ),
        tags: ['Transactions'],
        responses: [
            new OpenApiSingleDataResponse(data: 'true', type: 'boolean'),
        ]
    )]
    public function delete(TransactionsDeleteRequest $request): JsonResponse
    {
        Transaction::query()->whereIn('id', $request->ids)->delete();

        return ApiResponse::success(true);
    }
}
