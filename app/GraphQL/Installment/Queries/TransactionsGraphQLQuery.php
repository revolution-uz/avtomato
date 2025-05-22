<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\TransactionGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\Branch;
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

    private array $_branches;

    public function type(): Type
    {
        return GraphQL::paginate(TransactionGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        $this->_branches = RbacHelper::getEnabledBranches(PermissionEnum::TRANSACTION_VIEW_ALL);

        return [
            'sort' => [
                'type' => Type::string(),
                'rules' => ['nullable', 'string', 'regex:/^[-]?[a-z_]+$/'],
            ],
            'search' => [
                'type' => Type::string(),
                'rules' => ['nullable', 'string', 'max:255'],
            ],
            'page' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', 'min:1'],
            ],
            'per_page' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', 'min:5', 'max:100'],
            ],
            'branches' => [
                'type' => Type::listOf(Type::nonNull(Type::int())),
                'rules' => ['nullable', 'array', 'distinct'],
            ],
            'admin_id' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', 'exists:admins,id'],
            ],
            'installment_id' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', 'exists:installments,id,deleted_at,NULL'],
            ],
            'customer_id' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', 'exists:customers,id,deleted_at,NULL'],
            ],
            'card_id' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', 'exists:cards,id,deleted_at,NULL'],
            ],
            'transaction_types' => [
                'type' => Type::listOf(Type::nonNull(Type::int())),
                'rules' => ['nullable', 'array', 'distinct'],
            ],
            'from' => [
                'type' => Type::string(),
                'rules' => ['nullable', 'required_with:to', 'date_format:Y-m-d'],
            ],
            'to' => [
                'type' => Type::string(),
                'rules' => ['nullable', 'required_with:from', 'date_format:Y-m-d', 'after_or_equal:from'],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $this->_branches = $args['branches'] ?? $this->_branches;

        return Transaction::query()
            ->with(['installment', 'admin', 'customer', 'transactionType', 'installment.branch'])
            ->when(
                isset($args['admin_id']),
                fn (Builder $q) => $q
                    ->where('admin_id', $args['admin_id'])
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
                isset($args['transaction_types']),
                fn (Builder $q) => $q
                    ->whereIn('transaction_type_id', $args['transaction_types'])
            )
            ->when(
                $this->_branches,
                fn (Builder $q) => $q
                    ->where(fn (Builder $q) => $q
                        ->whereIn('branch_id', $this->_branches)
                        ->orWhere(fn (Builder $q) => $q
                            ->whereNull('branch_id')
                            ->whereHas('installment', fn (Builder $q) => $q->whereIn('branch_id', $this->_branches))
                        )
                    )
            )
            ->when(
                isset($args['from'], $args['to']),
                fn (Builder|Transaction $q) => $q
                    ->whereDate('paid_at', '>=', $args['from'])
                    ->whereDate('paid_at', '<=', $args['to'])
            )
            ->when(
                isset($args['sort']),
                fn (Builder|Transaction $q) => $q->orderBySortable($args['sort']),
                fn (Builder|Transaction $q) => $q->orderByDesc('paid_at')
            )
            ->when(
                isset($args['search']),
                fn ($q) => $q
                    ->where('id', 'ilike', "%{$args['search']}%")
                    ->orWhere('external_id', 'ilike', "%{$args['search']}%")
                    ->orWhereHas('branch', fn ($q) => $q->where('name', 'ilike', "%{$args['search']}%"))
                    ->orWhereHas('installment', fn ($q) => $q->where('id', 'ilike', "%{$args['search']}%"))
                    ->orWhereHas('customer', fn ($q) => $q
                        ->where('first_name', 'ilike', "%{$args['search']}%")
                        ->orWhere('last_name', 'ilike', "%{$args['search']}%")
                    )
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }

    protected function rules(array $args = []): array
    {
        return [
            'branches.*' => [
                'required',
                'int',
                Rule::exists(Branch::class, 'id')
                    ->whereNull('deleted_at')
                    ->when($this->_branches, fn ($q) => $q->whereIn('id', $this->_branches)),
            ],
            'transaction_types.*' => [
                'required',
                'int',
                'exists:transaction_types,id,deleted_at,NULL',
            ],
        ];
    }
}
