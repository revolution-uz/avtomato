<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\TransactionGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Transaction;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class TransactionsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'transactions',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(TransactionGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'sort' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|regex:/^[-]?[a-z_]+$/',
            ],
            'search' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|max:255',
            ],
            'page' => [
                'type' => Type::int(),
                'rules' => 'nullable|int|min:1',
            ],
            'per_page' => [
                'type' => Type::int(),
                'rules' => 'nullable|int|min:5|max:100',
            ],
            'id' => [
                'type' => Type::int(),
                'rules' => 'int|min:1',
            ],
            'installment_id' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', Rule::exists('installments', 'id')->whereNull('deleted_at')],
            ],
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::int(),
            ],
            'card_id' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', Rule::exists('cards', 'id')->whereNull('deleted_at')],
            ],
            'transaction_type_id' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', Rule::exists('transaction_types', 'id')->whereNull('deleted_at')],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::TRANSACTION_VIEW_ALL);

        return Transaction::query()
            ->with(['installment', 'admin', 'customer', 'transactionType', 'installment.branch'])
            ->when(
                isset($args['amount']),
                fn (Builder $q) => $q
                    ->where('amount', $args['amount'])
            )
            ->when(
                isset($args['id']),
                fn (Builder $q) => $q
                    ->where('id', $args['id'])
            )
            ->when(
                isset($args['branch_id']),
                fn (Builder $q) => $q
                    ->whereHas('installment', fn (Builder $q) => $q->where('branch_id', $args['branch_id']))
            )
            ->when(
                isset($args['admin_id']),
                fn (Builder $q) => $q
                    ->where('id', $args['admin_id'])
            )
            ->when(
                isset($args['installment_id']),
                fn (Builder $q) => $q
                    ->where('installment_id', $args['installment_id'])
            )
            ->when(
                isset($args['customer_id']),
                fn (Builder $q) => $q
                    ->where('customer_id', $args['customer_id'])
            )
            ->when(
                isset($args['card_id']),
                fn (Builder $q) => $q
                    ->where('card_id', $args['card_id'])
            )
            ->when(
                isset($args['transaction_type_id']),
                fn (Builder $q) => $q
                    ->where('transaction_type_id', $args['transaction_type_id'])
            )
            ->when($branches,
                fn (Builder $q) => $q->whereHas('installment',
                    fn (Builder $q) => $q->whereIn('branch_id', $branches)
                ))
            ->when(isset($args['sort']), fn (Builder|Transaction $q) => $q->orderBySortable($args['sort']))

            ->when(isset($args['search']), function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->where(function (Builder $query) use ($search) {
                    $query->where('id', 'ILIKE', $search)
                        ->orWhere('external_id', 'ILIKE', $search);
                });
            })

            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
