<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use App\GraphQL\Management\Types\AdminGraphQLType;
use App\Models\Company\LoyaltyProgram\OrderStatusHistory;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderStatusHistoryGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'OrderStatusHistoryGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderStatusHistory $root) => $root->id,
            ],
            'order_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderStatusHistory $root) => $root->order_id,
            ],
            'admin_id' => [
                'type' => Type::int(),
                'resolve' => fn (OrderStatusHistory $root) => $root->admin_id,
            ],
            'from_status' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderStatusHistory $root) => $root->from_status->value,
            ],
            'to_status' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderStatusHistory $root) => $root->to_status->value,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderStatusHistory $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderStatusHistory $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (OrderStatusHistory $root) => $root->deleted_at?->timestamp,
            ],
            'order' => [
                'type' => Type::nonNull(GraphQL::type(OrderGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (OrderStatusHistory $root) => $root->order,
            ],
            'admin' => [
                'type' => GraphQL::type(AdminGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (OrderStatusHistory $root) => $root->admin,
            ],
        ];
    }
}
