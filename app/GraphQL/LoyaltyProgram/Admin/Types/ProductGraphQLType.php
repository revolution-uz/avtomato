<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use App\Models\Company\LoyaltyProgram\Item;
use GraphQL\Type\Definition\Type;
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
                'resolve' => fn (Item $root) => $root->id,
            ],
            'external_code' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Item $root) => $root->external_code,
            ],
            'external_shop_id' => [
                'type' => Type::int(),
                'resolve' => fn (Item $root) => $root->external_shop_id,
            ],
            'external_art' => [
                'type' => Type::string(),
                'resolve' => fn (Item $root) => $root->external_art,
            ],
            'external_name' => [
                'type' => Type::string(),
                'resolve' => fn (Item $root) => $root->external_name,
            ],
            'external_mxik' => [
                'type' => Type::string(),
                'resolve' => fn (Item $root) => $root->external_mxik,
            ],
            'external_package_code' => [
                'type' => Type::string(),
                'resolve' => fn (Item $root) => $root->external_package_code,
            ],
            'external_gtin' => [
                'type' => Type::string(),
                'resolve' => fn (Item $root) => $root->external_gtin,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Item $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Item $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (Item $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
