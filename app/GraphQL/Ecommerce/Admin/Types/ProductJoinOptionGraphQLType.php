<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\Models\Company\Ecommerce\ProductJoinOption;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProductJoinOptionGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'ProductJoinOptionGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductJoinOption $root) => $root->id,
            ],
            'option_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductJoinOption $root) => $root->option_id,
            ],
            'value_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductJoinOption $root) => $root->value_id,
            ],
            'product_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductJoinOption $root) => $root->product_id,
            ],
            'product_variation_id' => [
                'type' => Type::int(),
                'resolve' => fn (ProductJoinOption $root) => $root->product_variation_id,
            ],
            'product' => [
                'type' => Type::nonNull(GraphQL::type(ProductGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (ProductJoinOption $root) => $root->product,
            ],
            'product_option_value' => [
                'type' => Type::nonNull(GraphQL::type(ProductOptionValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (ProductJoinOption $root) => $root->productOptionValue,
            ],
            'product_option' => [
                'type' => Type::nonNull(GraphQL::type(ProductOptionGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (ProductJoinOption $root) => $root->productOption,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductJoinOption $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductJoinOption $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (ProductJoinOption $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
