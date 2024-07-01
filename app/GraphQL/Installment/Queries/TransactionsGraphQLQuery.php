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
                'name' => 'sort',
                'type' => Type::string(),
            ],
            'search' => [
                'name' => 'search',
                'type' => Type::string(),
            ],
            'page' => [
                'name' => 'page',
                'type' => Type::int(),
            ],
            'per_page' => [
                'name' => 'per_page',
                'type' => Type::int(),
            ],

            'id' => [
                'name' => 'id',
                'type' => Type::int(),
            ],
            'installment_id' => [
                'name' => 'installment_id',
                'type' => Type::int(),
            ],
            'customer_id' => [
                'name' => 'customer_id',
                'type' => Type::int(),
            ],
            'card_id' => [
                'name' => 'card_id',
                'type' => Type::int(),
            ],
            'transaction_type_id' => [
                'name' => 'transaction_type_id',
                'type' => Type::int(),
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
            ->with(['installment', 'employee', 'customer', 'transactionType', 'installment.branch'])
            ->when(
                $args['amount'],
                fn (Builder $q) => $q
                    ->where('amount', $args['amount'])
            )
            ->when(
                $args['id'],
                fn (Builder $q) => $q
                    ->where('id', $args['id'])
            )
            ->when(
                $args['branch_id'],
                fn (Builder $q) => $q
                    ->whereHas('installment', fn (Builder $q) => $q->where('branch_id', $args['branch_id']))
            )
            ->when(
                $args['admin_id'],
                fn (Builder $q) => $q
                    ->where('id', $args['admin_id'])
            )
            ->when(
                $args['installment_id'],
                fn (Builder $q) => $q
                    ->where('installment_id', $args['installment_id'])
            )
            ->when(
                $args['customer_id'],
                fn (Builder $q) => $q
                    ->where('customer_id', $args['customer_id'])
            )
            ->when(
                $args['card_id'],
                fn (Builder $q) => $q
                    ->where('card_id', $args['card_id'])
            )
            ->when(
                $args['transaction_type_id'],
                fn (Builder $q) => $q
                    ->where('transaction_type_id', $args['transaction_type_id'])
            )
            ->when($branches,
                fn (Builder $q) => $q->whereHas('installment',
                    fn (Builder $q) => $q->whereIn('branch_id', $branches)
                ))
            ->when(isset($args['sort']), fn (Builder|Transaction $q) => $q->orderBySortable($args['sort']))

            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }

    protected function rules(array $args = []): array
    {
        return [
            'sort' => 'nullable|string|regex:/^[-]?[a-z_]+$/',
            'search' => 'nullable|string|max:255',
            'page' => 'nullable|int|min:1',
            'per_page' => 'nullable|int|min:5|max:100',
            'id' => 'int|min:1',
            'installment_id' => ['nullable', 'int', Rule::exists('installments', 'id')],
            'customer_id' => ['nullable', 'int', Rule::exists('customers', 'id')],
            'card_id' => ['nullable', 'int', Rule::exists('cards', 'id')],
            'transaction_type_id' => ['nullable', 'int', Rule::exists('transaction_types', 'id')],
        ];
    }
}
