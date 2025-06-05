<?php

namespace App\GraphQL\QrPay\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\TransactionAttemptStatusEnum;
use App\GraphQL\QrPay\Types\SbpTransactionGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\SbpTransaction;
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
        return GraphQL::paginate(SbpTransactionGraphQLType::GRAPHQL_TYPE_NAME);
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
            'status' => [
                'type' => Type::int(),
                'rules' => [
                    'nullable',
                    'int',
                    Rule::in(TransactionAttemptStatusEnum::cases()),
                ],
            ],
            'from' => [
                'type' => Type::string(),
                'rules' => 'nullable|date_format:Y-m-d',
            ],
            'to' => [
                'type' => Type::string(),
                'rules' => 'nullable|date_format:Y-m-d|after_or_equal:from',
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::QR_PAY_TRANSACTION_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return SbpTransaction::query()
            ->when(
                isset($args['sort']),
                fn (Builder|SbpTransaction $q) => $q->orderBySortable($args['sort']),
                fn (Builder|SbpTransaction $q) => $q->orderByDesc('id'))
            ->when(
                isset($args['search']),
                fn ($q) => $q
                    ->where('id', 'ilike', "%{$args['search']}%")
                    ->orWhere('ext_id', 'ilike', "%{$args['search']}%"))
            ->when(isset($args['from']), fn (Builder|Transaction $q) => $q
                ->whereDate('payment_updated_at', '>=', $args['from']))
            ->when(isset($args['to']), fn (Builder|Transaction $q) => $q
                ->whereDate('payment_updated_at', '<=', $args['to']))
            ->when(
                isset($args['status']),
                fn (Builder|Transaction $q) => $q
                    ->where('status', $args['status']))
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
