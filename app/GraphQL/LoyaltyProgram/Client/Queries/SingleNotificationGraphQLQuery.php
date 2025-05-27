<?php

namespace App\GraphQL\LoyaltyProgram\Client\Queries;

use App\GraphQL\LoyaltyProgram\Client\Types\NotificationGraphQLType;
use App\Models\Company\LoyaltyProgram\Notification;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class SingleNotificationGraphQLQuery extends Query
{
    protected $attributes = [
        'name' => 'singleNotification',
    ];

    public function type(): Type
    {
        return Type::nonNull(GraphQL::type(NotificationGraphQLType::GRAPHQL_TYPE_NAME));
    }

    public function args(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'rules' => ['required', 'exists:loyalty_program_notifications,id,send_at,NOT_NULL'],
            ],
        ];
    }

    public function resolve($root, array $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        return Notification::query()->findOrFail($args['id']);
    }
}
