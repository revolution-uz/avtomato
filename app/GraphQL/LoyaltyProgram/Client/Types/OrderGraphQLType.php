<?php

namespace App\GraphQL\LoyaltyProgram\Client\Types;

use App\GraphQL\LoyaltyProgram\Admin\Types\OrderItemGraphQLType;
use App\Models\Company\LoyaltyProgram\Order;
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
            'admin_id' => [
                'type' => Type::int(),
                'resolve' => fn (Order $root) => $root->admin_id,
            ],
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Order $root) => $root->status->value,
            ],
            'discount_type' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Order $root) => $root->discount_type->value,
            ],
            'total_amount' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (Order $root) => $root->total_amount,
            ],
            'total_discount_amount' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (Order $root) => $root->total_discount_amount,
            ],
            'total_paid_amount' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (Order $root) => $root->total_paid_amount,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Order $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Order $root) => $root->updated_at->timestamp,
            ],
            'items' => [
                'type' => Type::listOf(GraphQL::type(OrderItemGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Order $root) => $root->items,
            ],
        ];
    }
}
