<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\GraphQL\GraphQLTypeHelper;
use App\Models\Company\Ecommerce\ProductOption;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProductOptionGraphQLType extends GraphQLType
{
    use GraphQLTypeHelper;

    public const string GRAPHQL_TYPE_NAME = 'ProductOptionGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductOption $root) => $root->id,
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOption $root) => $root->name,
            ],
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOption $root) => $root->name_uz,
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOption $root) => $root->name_ru,
            ],
            'name_en' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOption $root) => $root->name_en,
            ],
            'name_oz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOption $root) => $root->name_oz,
            ],
            'slug' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOption $root) => $root->slug,
            ],
            'description' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOption $root) => $root->description,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductOption $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductOption $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (ProductOption $root) => $root->deleted_at?->timestamp,
            ],
            'values' => [
                'type' => Type::listOf(GraphQL::type(ProductOptionValueGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (ProductOption $root) => $root->values,
            ],
            'categories' => [
                'type' => Type::listOf(GraphQL::type(ProductCategoryGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (ProductOption $root) => $root->categories,
            ],
        ];
    }
}
