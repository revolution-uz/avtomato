<?php

namespace App\GraphQL\Ecommerce\Client\Types;

use App\GraphQL\Ecommerce\Admin\Types\ProductGraphQLType;
use App\GraphQL\Ecommerce\Admin\Types\ProductVariationGraphQLType;
use App\Models\Company\Ecommerce\UserCart;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserCartGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'UserCartGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (UserCart $root) => $root->id,
            ],
            'quantity' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (UserCart $root) => $root->quantity,
            ],
            'user_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (UserCart $root) => $root->user_id,
            ],
            'product_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (UserCart $root) => $root->product_id,
            ],
            'variation_id' => [
                'type' => Type::int(),
                'resolve' => fn (UserCart $root) => $root->variation_id,
            ],
            'user' => [
                'type' => Type::nonNull(UserGraphQLType::type()),
                'resolve' => fn (UserCart $root) => $root->user,
            ],
            'product' => [
                'type' => Type::nonNull(ProductGraphQLType::type()),
                'resolve' => fn (UserCart $root) => $root->product,
            ],
            'variation' => [
                'type' => GraphQl::type(ProductVariationGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (UserCart $root) => $root->variation,
            ],
        ];
    }
}
