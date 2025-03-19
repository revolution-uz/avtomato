<?php

namespace App\GraphQL\LoyaltyProgram\Client\Types;

use App\GraphQL\LoyaltyProgram\Admin\Types\ProductGraphQLType;
use App\Models\Company\LoyaltyProgram\OrderItem;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class OrderItemGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'OrderItemGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderItem $root) => $root->id,
            ],
            'order_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderItem $root) => $root->order_id,
            ],
            'product_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderItem $root) => $root->product_id,
            ],
            'quantity' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderItem $root) => $root->quantity,
            ],
            'price' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (OrderItem $root) => $root->price,
            ],
            'discount_amount' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (OrderItem $root) => $root->discount_amount,
            ],
            'total_amount' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (OrderItem $root) => $root->total_amount,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderItem $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderItem $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (OrderItem $root) => $root->deleted_at?->timestamp,
            ],
            'order' => [
                'type' => Type::nonNull(GraphQL::type(OrderGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (OrderItem $root) => $root->order,
            ],
            'product' => [
                'type' => Type::nonNull(GraphQL::type(ProductGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (OrderItem $root) => $root->product,
            ],
        ];
    }
}
