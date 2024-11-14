<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\Models\Company\Ecommerce\OrderItem;
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
            'product_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderItem $root) => $root->product_id,
            ],
            'product' => [
                'type' => Type::nonNull(GraphQL::type(ProductGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (OrderItem $root) => $root->product,
            ],
            'price' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (OrderItem $root) => $root->price,
            ],
            'quantity' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (OrderItem $root) => $root->quantity,
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
        ];
    }
}
