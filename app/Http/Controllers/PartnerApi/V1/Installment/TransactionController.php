<?php

namespace App\Http\Controllers\PartnerApi\V1\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Helpers\Swagger\OpenApiArrayResponse;
use App\Helpers\Swagger\OpenApiCustomParameter;
use App\Helpers\Swagger\OpenApiDataResponse;
use App\Helpers\Swagger\OpenApiIdParameter;
use App\Helpers\Swagger\OpenApiNotFoundResponse;
use App\Helpers\Swagger\OpenApiPageParameter;
use App\Helpers\Swagger\OpenApiPerPageParameter;
use App\Helpers\Swagger\OpenApiSearchParameter;
use App\Helpers\Swagger\OpenApiSortParameter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Installment\TransactionFilterRequest;
use App\Http\Requests\Installment\TransactionRequest;
use App\Http\Requests\Installment\TransactionsDeleteRequest;
use App\Http\Requests\Installment\TransactionsReverseRequest;
use App\Http\Requests\Installment\TransactionViewRequest;
use App\Http\Resources\Installment\TransactionResource;
use App\Http\Resources\StatusResource;
use App\Models\Company\Transaction;
use App\Services\AtmosService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;

class TransactionController extends Controller
{
    public function __construct(
        private readonly AtmosService $atmosCardService,
    ) {}

    #[OA\Get(
        path: '/api/v1/installment/transaction',
        description: 'Transaction route',
        tags: ['Transactions'],
        parameters: [
            new OpenApiPageParameter,
            new OpenApiPerPageParameter,
            new OpenApiSearchParameter,
            new OpenApiSortParameter,
            new OpenApiCustomParameter('id'),
            new OpenApiCustomParameter('branch_id'),
            new OpenApiCustomParameter('admin_id'),
            new OpenApiCustomParameter('installment_id'),
            new OpenApiCustomParameter('customer_id'),
            new OpenApiCustomParameter('card_id'),
            new OpenApiCustomParameter('created_at'),
            new OpenApiCustomParameter('transaction_type_id'),
            new OpenApiCustomParameter('amount'),
        ],
        responses: [
            new OpenApiArrayResponse(resourceName: 'TransactionResource', withPagination: true, withSortables: true),
        ]
    )]
    public function index(TransactionFilterRequest $request): AnonymousResourceCollection
    {
        $request->sortables = ['id', 'installment_id', 'customer_id', 'card_id', 'transaction_type_id', 'amount', 'on_created']; // TODO add all fields, it's just for testing
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::TRANSACTION_VIEW_ALL);

        return TransactionResource::collection(
            Transaction::query()
                ->with(['installment', 'admin', 'customer', 'transactionType', 'installment.branch'])
                ->when(
                    $request->amount,
                    fn (Builder $q) => $q
                        ->where('amount', $request->amount)
                )
                ->when(
                    $request->id,
                    fn (Builder $q) => $q
                        ->where('id', $request->id)
                )
                ->when(
                    $request->branch_id,
                    fn (Builder $q) => $q
                        ->whereHas('installment', fn (Builder $q) => $q->where('branch_id', $request->branch_id))
                )
                ->when(
                    $request->admin_id,
                    fn (Builder $q) => $q
                        ->where('id', $request->admin_id)
                )
                ->when(
                    $request->installment_id,
                    fn (Builder $q) => $q
                        ->where('installment_id', $request->installment_id)
                )
                ->when(
                    $request->customer_id,
                    fn (Builder $q) => $q
                        ->where('customer_id', $request->customer_id)
                )
                ->when(
                    $request->card_id,
                    fn (Builder $q) => $q
                        ->where('card_id', $request->card_id)
                )
                ->when(
                    $request->transaction_type_id,
                    fn (Builder $q) => $q
                        ->where('transaction_type_id', $request->transaction_type_id)
                )
                ->when($request->isSortable(), function (Builder $q) use ($request) {
                    $q->orderBy($request->sortField(), $request->sortDirection());
                })
                ->when($branches,
                    fn (Builder $q) => $q->whereHas('installment',
                        fn (Builder $q) => $q->whereIn('branch_id', $branches)
                    ))
                ->paginate($request->per_page)
        )->additional(['sortables' => $request->sortables]);
    }

    #[OA\Get(
        path: '/api/v1/installment/transaction/{id}',
        description: 'Transaction get single route',
        tags: ['Transactions'],
        parameters: [
            new OpenApiIdParameter(modelName: 'Transaction'),
        ],
        responses: [
            new OpenApiDataResponse(resourceName: 'TransactionResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function getSingle(int $id, TransactionViewRequest $request): TransactionResource
    {
        return new TransactionResource(Transaction::findOrFail($id));
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
            new OpenApiDataResponse(resourceName: 'TransactionResource'),
        ]
    )]
    public function create(TransactionRequest $request): TransactionResource
    {
        $model = new Transaction;
        $model->fill($request->validated());
        $model->external_id = 'local_' . Str::random();
        $model->saveOrFail();

        return new TransactionResource($model);
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
            new OpenApiDataResponse(resourceName: 'TransactionResource'),
            new OpenApiNotFoundResponse,
        ]
    )]
    public function update(int $id, TransactionRequest $request): TransactionResource
    {
        /** @var Transaction $transaction */
        $transaction = Transaction::findOrFail($id);
        $transaction->fill($request->validated());
        $transaction->saveOrFail();

        return new TransactionResource($transaction);
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
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function delete(TransactionsDeleteRequest $request): StatusResource
    {
        Transaction::query()->whereIn('id', $request->ids)->delete();

        return new StatusResource(true);
    }

    #[OA\Delete(
        path: '/api/v1/installment/transaction/{id}/reverse',
        description: 'Transaction reverse route',
        parameters: [
            new OpenApiIdParameter(modelName: 'Transaction'),
        ],
        tags: ['Transactions'],
        responses: [
            new OpenApiDataResponse(resourceName: 'StatusResource'),
        ]
    )]
    public function reverse(int $id, TransactionsReverseRequest $request): StatusResource
    {
        $this->atmosCardService->reversePayment(Transaction::findOrFail($id));

        return new StatusResource(true);
    }
}
