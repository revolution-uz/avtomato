<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\Models\Company\Ecommerce\Product;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProductGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'ProductGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Product $root) => $root->id,
            ],
            'category_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Product $root) => $root->category_id,
            ],
            'brand_id' => [
                'type' => Type::int(),
                'resolve' => fn (Product $root) => $root->brand_id,
            ],
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Product $root) => $root->name_uz,
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Product $root) => $root->name_ru,
            ],
            'name_en' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Product $root) => $root->name_en,
            ],
            'name_oz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Product $root) => $root->name_oz,
            ],
            'slug' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Product $root) => $root->slug,
            ],
            'main_image' => [
                'type' => GraphQL::type(EcommerceImageGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Product $root) => $root->mainImage,
            ],
            'images' => [
                'type' => Type::listOf(GraphQL::type(EcommerceImageGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Product $root) => $root->images,
            ],
            'variations' => [
                'type' => Type::listOf(GraphQL::type(ProductVariationGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Product $root) => $root->variations,
            ],
            'is_active' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Product $root) => $root->is_active,
            ],
            'cost' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (Product $root) => $root->cost,
            ],
            'actual_price' => [
                'type' => Type::nonNull(Type::float()),
                'resolve' => fn (Product $root) => $root->actual_price,
            ],
            'old_price' => [
                'type' => Type::float(),
                'resolve' => fn (Product $root) => $root->old_price,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Product $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Product $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (Product $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
