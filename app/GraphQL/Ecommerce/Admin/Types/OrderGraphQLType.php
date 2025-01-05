<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\GraphQL\Customer\Types\AddressGraphQLType;
use App\GraphQL\Ecommerce\Client\Types\UserGraphQLType;
use App\Models\Company\Ecommerce\Order;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'OrderGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Order $root) => $root->id,
            ],
            'user_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Order $root) => $root->user_id,
            ],
            'address_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Order $root) => $root->address_id,
            ],
            'status_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Order $root) => $root->status_id,
            ],
            'installment_id' => [
                'type' => Type::int(),
                'resolve' => fn (Order $root) => $root->installment_id,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Order $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Order $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (Order $root) => $root->deleted_at?->timestamp,
            ],
            'user' => [
                'type' => Type::nonNull(UserGraphQLType::type()),
                'resolve' => fn (Order $root) => $root->user,
            ],
            'address' => [
                'type' => Type::nonNull(AddressGraphQLType::type()),
                'resolve' => fn (Order $root) => $root->address,
            ],
            'status' => [
                'type' => Type::nonNull(OrderStatusGraphQLType::type()),
                'resolve' => fn (Order $root) => $root->status,
            ],
            // 'installment' => [
            //     'type' => GraphQL::type(InstallmentGraphQLType::GRAPHQL_TYPE_NAME),
            //     'resolve' => fn(Order $root) => $root->installment,
            // ],
            'status_histories' => [
                'type' => Type::listOf(GraphQL::type(OrderStatusHistoryGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Order $root) => $root->statusHistories,
            ],
            'order_items' => [
                'type' => Type::listOf(GraphQL::type(OrderItemGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Order $root) => $root->orderItems,
            ],
        ];
    }
}
