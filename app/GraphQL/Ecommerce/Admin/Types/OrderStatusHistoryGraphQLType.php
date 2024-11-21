<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\GraphQL\Management\Types\AdminGraphQLType;
use App\Models\Company\Ecommerce\OrderStatusHistory;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderStatusHistoryGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'OrderStatusGraphQLType';

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
            'from_status_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderStatusHistory $root) => $root->from_status_id,
            ],
            'from_status' => [
                'type' => Type::nonNull(GraphQL::type(OrderStatusGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (OrderStatusHistory $root) => $root->fromStatus,
            ],
            'to_status_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderStatusHistory $root) => $root->to_status_id,
            ],
            'to_status' => [
                'type' => Type::nonNull(GraphQL::type(OrderStatusGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (OrderStatusHistory $root) => $root->toStatus,
            ],
            'admin_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderStatusHistory $root) => $root->admin_id,
            ],
            'admin' => [
                'type' => GraphQL::type(AdminGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (OrderStatusHistory $root) => $root->admin,
            ],
            'order_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderStatusHistory $root) => $root->order_id,
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
        ];
    }
}
