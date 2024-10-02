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
            'branches' => [
                'type' => Type::listOf(Type::nonNull(Type::int())),
                'rules' => 'nullable|array',
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
            'from' => [
                'name' => 'from',
                'type' => Type::string(),
                'rules' => 'nullable|required_with:to|date_format:Y-m-d',
            ],
            'to' => [
                'name' => 'to',
                'type' => Type::string(),
                'rules' => 'nullable|required_with:from|date_format:Y-m-d|after_or_equal:from',
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if (isset($args['branches'])) {
            $args['branches'] = array_intersect($args['branches'], $this->_branches);
        }

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
            ->when(
                isset($args['branches']),
                fn (Builder $q) => $q
                    ->whereIn('branch_id', $args['branches'])
                    ->orWhere(fn (Builder $q) => $q
                        ->whereNull('branch_id')
                        ->whereHas('installment', fn (Builder $q) => $q
                            ->whereIn('branch_id', $args['branches'])
                        )
                    )
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
            ->when(
                isset($args['from'], $args['to']),
                fn (Builder|Transaction $q) => $q
                    ->whereDate('paid_at', '>=', $args['from'])
                    ->whereDate('paid_at', '<=', $args['to'])
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }

    protected function rules(array $args = []): array
    {
        return [
            'branches.*' => [
                'required',
                Rule::exists('branches', 'id')
                    ->whereNull('deleted_at')
                    ->when($this->_branches, fn ($q) => $q->whereIn('id', $this->_branches)),
            ],
        ];
    }
}
