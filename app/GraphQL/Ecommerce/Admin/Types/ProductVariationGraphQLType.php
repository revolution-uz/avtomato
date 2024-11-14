<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\Models\Company\Ecommerce\ProductVariation;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProductVariationGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'ProductOptionValueGraphQLType';

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
            'product_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductVariation $root) => $root->product_id,
            ],
            'is_active' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (ProductVariation $root) => $root->is_active,
            ],
            'product' => [
                'type' => GraphQL::type(ProductGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (ProductVariation $root) => $root->product,
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
        ];
    }
}
