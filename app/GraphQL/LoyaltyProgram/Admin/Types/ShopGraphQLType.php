<?php

namespace App\GraphQL\LoyaltyProgram\Admin\Types;

use App\GraphQL\LoyaltyProgram\Admin\Mutations\LoyaltyProgramImageGraphQLType;
use App\Models\Company\LoyaltyProgram\Shop;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ShopGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'ShopGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Shop $root) => $root->id,
            ],
            'name' => [
                'type' => Type::string(),
                'resolve' => fn (Shop $root) => $root->name,
            ],
            'address' => [
                'type' => Type::string(),
                'resolve' => fn (Shop $root) => $root->address,
            ],
            'lat' => [
                'type' => Type::float(),
                'resolve' => fn (Shop $root) => $root->lat,
            ],
            'lng' => [
                'type' => Type::float(),
                'resolve' => fn (Shop $root) => $root->lng,
            ],
            'status' => [
                'type' => Type::int(),
                'resolve' => fn (Shop $root) => $root->status->value,
            ],
            'image_id' => [
                'type' => Type::int(),
                'resolve' => fn (Shop $root) => $root->image_id,
            ],
            'external_code' => [
                'type' => Type::string(),
                'resolve' => fn (Shop $root) => $root->external_code,
            ],
            'external_name' => [
                'type' => Type::string(),
                'resolve' => fn (Shop $root) => $root->external_name,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Shop $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Shop $root) => $root->updated_at->timestamp,
            ],
            'image' => [
                'type' => GraphQL::type(LoyaltyProgramImageGraphQLType::GRAPHQL_TYPE_NAME),
                'resolve' => fn (Shop $root) => $root->image,
            ],
            'display_name' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (Shop $root) => $root->display_name,
            ],
        ];
    }
}
