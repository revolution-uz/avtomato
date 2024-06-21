<?php

namespace App\GraphQL\Customer\Types;

use App\Models\Company\Phone;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PhoneGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'PhoneGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Phone GraphQL Type',
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'ID of phone',
                'resolve' => function (Phone $root) {
                    return $root->id;
                },
            ],
            'phone' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Phone number',
                'resolve' => function (Phone $root) {
                    return $root->phone;
                },
            ],
            'type' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'Type of phone',
                'resolve' => function (Phone $root) {
                    return $root->pivot->type;
                },
            ],
            'comment' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'Comment of phone',
                'resolve' => function (Phone $root) {
                    return $root->pivot->comment;
                },
            ],
        ];
    }
}
