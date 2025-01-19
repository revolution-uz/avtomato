<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Ecommerce\ProductCategory;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProductCategoryGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'ProductCategoryGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductCategory $root) => $root->id,
            ],
            'parent_id' => [
                'type' => Type::int(),
                'resolve' => fn (ProductCategory $root) => $root->parent_id,
            ],
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductCategory $root) => $root->name_uz,
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductCategory $root) => $root->name_ru,
            ],
            'name_en' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductCategory $root) => $root->name_en,
            ],
            'name_oz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductCategory $root) => $root->name_oz,
            ],
            'is_active' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (ProductCategory $root) => $root->is_active,
            ],
            'image_id' => [
                'type' => Type::int(),
                'resolve' => fn (ProductCategory $root) => $root->image_id,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductCategory $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductCategory $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (ProductCategory $root) => $root->deleted_at?->timestamp,
            ],
            'image' => [
                'type' => GraphQL::type(EcommerceImageGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (ProductCategory $root) => $root->image,
            ],
            'parent' => [
                'type' => GraphQL::type(ProductCategoryGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (ProductCategory $root) => $root->parent,
            ],
            'products' => [
                'type' => Type::listOf(GraphQL::type(ProductGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (ProductCategory $root) => $root->products,
            ],
            'banners' => [
                'type' => Type::listOf(GraphQL::type(BannerGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (ProductCategory $root) => $root->banners,
            ],
        ];
    }
}
