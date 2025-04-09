<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Queries;

use App\Enums\Company\PaymentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\GraphQL\LoyaltyProgram\Admin\Types\PaymentGraphQLType;
use App\Helpers\RbacHelper;
use App\Models\Company\LoyaltyProgram\LoyaltyProgramPayment;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class PaymentsGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'payments',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(PaymentGraphQLType::GRAPHQL_TYPE_NAME);
    }

    public function args(): array
    {
        return [
            'sort' => [
                'type' => Type::string(),
                'rules' => 'nullable|string|regex:/^[-]?[a-z_]+$/',
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
                'rules' => ['nullable', 'int', Rule::enum(PaymentStatusEnum::class)],
            ],
            'card_id' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', 'exists:loyalty_program_cards,id,deleted_at,NULL'],
            ],
            'subscription_id' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', 'exists:loyalty_program_subscriptions,id'],
            ],
            'user_id' => [
                'type' => Type::int(),
                'rules' => ['nullable', 'int', 'exists:loyalty_program_users,id,deleted_at,NULL'],
            ],
        ];
    }

    public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::LOYALTY_PROGRAM_PAYMENT_VIEW_ALL);
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return LoyaltyProgramPayment::query()
            ->when(isset($args['status']), fn (Builder $q) => $q->where('status', $args['status']))
            ->when(isset($args['card_id']), fn (Builder $q) => $q->where('card_id', $args['card_id']))
            ->when(isset($args['subscription_id']), fn (Builder $q) => $q->where('subscription_id', $args['subscription_id']))
            ->when(isset($args['user_id']), fn (Builder $q) => $q->where('user_id', $args['user_id']))
            ->when(
                isset($args['sort']),
                fn (Builder|LoyaltyProgramPayment $q) => $q->orderBySortable($args['sort']),
                fn (Builder|LoyaltyProgramPayment $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
