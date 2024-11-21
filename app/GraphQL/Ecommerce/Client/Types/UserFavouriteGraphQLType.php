<?php

namespace App\GraphQL\Ecommerce\Client\Types;

use App\GraphQL\Ecommerce\Admin\Types\ProductGraphQLType;
use App\Models\Company\Ecommerce\UserCart;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserFavouriteGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'UserFavouriteGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'product_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (UserCart $root) => $root->product_id,
            ],
            'product' => [
                'type' => GraphQl::type(ProductGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (UserCart $root) => $root->product,
            ],
        ];
    }
}
