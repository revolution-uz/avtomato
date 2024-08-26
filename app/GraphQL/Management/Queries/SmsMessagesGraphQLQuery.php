<?php

namespace App\GraphQL\Management\Queries;

use App\GraphQL\Management\Types\SmsMessageGraphQLType;
use App\Models\Company\SmsMessage;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Illuminate\Database\Eloquent\Builder;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SmsMessagesGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'smsMessages',
    ];

    public function type(): Type
    {
        return GraphQL::paginate(SmsMessageGraphQLType::GRAPHQL_TYPE_NAME);
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
        ];
    }

    // public function authorize($root, array $args, $ctx, ?ResolveInfo $resolveInfo = null, ?Closure $getSelectFields = null): bool
    // {
    //     return RbacHelper::checkPermission(PermissionEnum::ADMIN_VIEW_ALL);
    // }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return SmsMessage::query()
            ->when(isset($args['search']), fn (Builder|SmsMessage $q) => $q
                ->where('phone', 'ILIKE', "%{$args['search']}%")
                ->orWhere('message', 'ILIKE', "%{$args['search']}%")
            )
            ->when(
                isset($args['sort']),
                fn (Builder|SmsMessage $q) => $q->orderBySortable($args['sort']),
                fn (Builder|SmsMessage $q) => $q->orderByDesc('id')
            )
            ->paginate(perPage: $args['per_page'] ?? 10, page: $args['page'] ?? 1);
    }
}
