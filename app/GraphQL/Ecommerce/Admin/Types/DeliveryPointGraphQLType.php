<?php

namespace App\GraphQL\Ecommerce\Admin\Types;

use App\GraphQL\Customer\Types\AddressGraphQLType;
use App\Models\Company\Ecommerce\DeliveryPoint;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DeliveryPointGraphQLType extends GraphQLType
{
    public const string GRAPHQL_TYPE_NAME = 'DeliveryPointGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (DeliveryPoint $root) => $root->id,
            ],
            'address_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (DeliveryPoint $root) => $root->address_id,
            ],
            'address' => [
                'type' => Type::nonNull(GraphQL::type(AddressGraphQLType::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (DeliveryPoint $root) => $root->address,
            ],
            'name_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (DeliveryPoint $root) => $root->name_uz,
            ],
            'name_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (DeliveryPoint $root) => $root->name_ru,
            ],
            'name_en' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (DeliveryPoint $root) => $root->name_en,
            ],
            'name_oz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (DeliveryPoint $root) => $root->name_oz,
            ],
            'description_uz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (DeliveryPoint $root) => $root->description_uz,
            ],
            'description_ru' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (DeliveryPoint $root) => $root->description_ru,
            ],
            'description_en' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (DeliveryPoint $root) => $root->description_en,
            ],
            'description_oz' => [
                'type' => Type::nonNull(Type::string()),
                'resolve' => fn (DeliveryPoint $root) => $root->description_oz,
            ],
            'is_prepaid' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (DeliveryPoint $root) => $root->is_prepaid,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (DeliveryPoint $root) => $root->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (DeliveryPoint $root) => $root->updated_at->timestamp,
            ],
            'deleted_at' => [
                'type' => Type::int(),
                'resolve' => fn (DeliveryPoint $root) => $root->deleted_at?->timestamp,
            ],
        ];
    }
}
