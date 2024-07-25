<?php

namespace App\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\Installment\Types\TransactionAttemptGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\TransactionAttempt;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class TransactionAttemptsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'transactionAttempts',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(TransactionAttemptGraphQLType::GRAPHQL_TYPE_NAME);
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
            'transaction_id' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', Rule::exists('transactions', 'id')->whereNull('deleted_at')],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TRANSACTION_ATTEMPT_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        $branches = RbacHelper::getEnabledBranches(PermissionEnum::TRANSACTION_ATTEMPT_VIEW_ALL);

        return TransactionAttempt::query()
            ->with(['installment', 'admin', 'customer', 'transaction', 'installment.branch'])
            ->when(
                isset($args['id']),
                fn (Builder $q) => $q
                    ->where('id', $args['id'])
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
                isset($args['transaction_id']),
                fn (Builder $q) => $q
                    ->where('transaction_id', $args['transaction_id'])
            )
            ->when($branches,
                fn (Builder $q) => $q->whereHas('installment',
                    fn (Builder $q) => $q->whereIn('branch_id', $branches)
                ))
            ->when(
                isset($args['sort']),
                fn (Builder|TransactionAttempt $q) => $q->orderBySortable($args['sort']),
                fn (Builder|TransactionAttempt $q) => $q->orderByDesc('id')
            )
            ->when(isset($args['search']), function (Builder $q) use ($args) {
                $search = '%' . $args['search'] . '%';
                $q->where(function (Builder $query) use ($search) {
                    $query->where('id', 'ILIKE', $search);
                });
            })
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
