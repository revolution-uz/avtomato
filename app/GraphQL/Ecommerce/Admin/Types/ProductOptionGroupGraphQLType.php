<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\Models\Company\Ecommerce\ProductOptionGroup;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProductOptionGroupGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'ProductOptionGroupGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductOptionGroup $root) => $root->id,
            ],
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOptionGroup $root) => $root->name_uz,
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOptionGroup $root) => $root->name_ru,
            ],
            'name_en' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOptionGroup $root) => $root->name_en,
            ],
            'name_oz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOptionGroup $root) => $root->name_oz,
            ],
            'slug' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOptionGroup $root) => $root->slug,
            ],
            'description' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductOptionGroup $root) => $root->description,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductOptionGroup $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductOptionGroup $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (ProductOptionGroup $root) => $root->deleted_at?->timestamp,
            ],
            'product_options' => [
                'type' => Type::listOf(GraphQL::type(ProductOptionGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (ProductOptionGroup $root) => $root->productOptions,
            ],
        ];
    }
}
