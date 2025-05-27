<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\Models\Company\Ecommerce\ProductVariation;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProductVariationGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'ProductVariationGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductVariation $root) => $root->id,
            ],
            'is_active' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (ProductVariation $root) => $root->is_active,
            ],
            'cost' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (ProductVariation $root) => $root->cost,
            ],
            'actual_price' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (ProductVariation $root) => $root->actual_price,
            ],
            'old_price' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (ProductVariation $root) => $root->old_price,
            ],
            'product_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductVariation $root) => $root->product_id,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductVariation $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductVariation $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (ProductVariation $root) => $root->deleted_at?->timestamp,
            ],
            'product' => [
                'type' => GraphQL::type(ProductGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (ProductVariation $root) => $root->product,
            ],
            'images' => [
                'type' => Type::listOf(GraphQL::type(EcommerceImageGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (ProductVariation $root) => $root->images,
            ],
            'option_values' => [
                'type' => Type::listOf(GraphQL::type(ProductOptionValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (ProductVariation $root) => $root->optionValues,
            ],
        ];
    }
}
