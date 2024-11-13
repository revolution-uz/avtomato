<?php

namespace App\GraphQL\Ecommerce\Types;

use App\Models\Company\Ecommerce\Banner;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class BannerGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'BannerGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Banner $root) => $root->id,
            ],
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Banner $root) => $root->name_uz,
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Banner $root) => $root->name_ru,
            ],
            'name_en' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Banner $root) => $root->name_en,
            ],
            'name_oz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Banner $root) => $root->name_oz,
            ],
            'is_active' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Banner $root) => $root->is_active,
            ],
            'url' => [
                'type' => Type::string(),
                'resolve' => fn (Banner $root) => $root->url,
            ],
            'category_id' => [
                'type' => Type::int(),
                'resolve' => fn (Banner $root) => $root->category_id,
            ],
            'brand_id' => [
                'type' => Type::int(),
                'resolve' => fn (Banner $root) => $root->brand_id,
            ],
            'product_id' => [
                'type' => Type::int(),
                'resolve' => fn (Banner $root) => $root->product_id,
            ],
            'category' => [
                'type' => GraphQL::type(ProductCategoryGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Banner $root) => $root->category,
            ],
            'brand' => [
                'type' => GraphQL::type(ProductBrandGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Banner $root) => $root->brand,
            ],
            'product' => [
                'type' => GraphQL::type(ProductGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Banner $root) => $root->product,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Banner $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Banner $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (Banner $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
