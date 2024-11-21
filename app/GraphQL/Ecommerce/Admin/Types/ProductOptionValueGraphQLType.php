<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\Models\Company\Ecommerce\ProductOptionValue;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProductOptionValueGraphQLType extends GraphQLType
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
                'resolve' => fn (ProductOptionValue $root) => $root->id,
            ],
            'value_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOptionValue $root) => $root->value_uz,
            ],
            'value_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOptionValue $root) => $root->value_ru,
            ],
            'value_en' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOptionValue $root) => $root->value_en,
            ],
            'value_oz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOptionValue $root) => $root->value_oz,
            ],
            'slug' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOptionValue $root) => $root->slug,
            ],
            'product_join_options' => [
                'type' => Type::listOf(GraphQL::type(ProductJoinOptionGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (ProductOptionValue $root) => $root->productJoinOptions(),
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductOptionValue $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductOptionValue $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (ProductOptionValue $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
