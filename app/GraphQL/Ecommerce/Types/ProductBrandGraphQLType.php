<?php

namespace App\GraphQL\Ecommerce\Types;

use App\Models\Company\Ecommerce\ProductBrand;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ProductBrandGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'ProductBrandGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductBrand $root) => $root->id,
            ],
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductBrand $root) => $root->name_uz,
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductBrand $root) => $root->name_ru,
            ],
            'name_en' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductBrand $root) => $root->name_en,
            ],
            'name_oz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductBrand $root) => $root->name_oz,
            ],
            'image' => [
                'type' => GraphQL::type(EcommerceImageGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (ProductBrand $root) => $root->image,
            ],
            'is_active' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (ProductBrand $root) => $root->is_active,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductBrand $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductBrand $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (ProductBrand $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
