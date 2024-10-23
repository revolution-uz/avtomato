<?php

namespace App\GraphQL\Market\Types;

use App\Models\Company\ProductBrand;
use GraphQL\Type\Definition\Type;

class ProductBrandGraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'BannerGraphQLType';

    protected array $attributes = [
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
            'status' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (ProductBrand $root) => $root->status,
            ],
            'image' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (ProductBrand $root) => $root->image,
            ],
        ];
    }
}
