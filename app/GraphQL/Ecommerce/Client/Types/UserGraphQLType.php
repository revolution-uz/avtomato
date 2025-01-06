<?php

namespace App\GraphQL\Ecommerce\Client\Types;

use App\GraphQL\Ecommerce\Admin\Types\OrderGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductGraphQLType;
use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Ecommerce\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'UserGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (User $root) => $root->id,
            ],
            'phone' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (User $root) => $root->phone,
            ],
            'customer_id' => [
                'type' => Type::int(),
                'resolve' => fn (User $root) => $root->customer_id,
            ],
            'orders' => [
                'type' => Type::listOf(GraphQL::type(OrderGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (User $root) => $root->orders,
            ],
            'carts' => [
                'type' => Type::listOf(GraphQL::type(ProductGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (User $root) => $root->carts,
            ],
            'favorites' => [
                'type' => Type::listOf(GraphQL::type(ProductGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (User $root) => $root->favorites,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (User $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (User $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (User $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
