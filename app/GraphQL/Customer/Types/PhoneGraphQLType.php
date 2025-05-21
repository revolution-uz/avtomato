<?php

namespace App\GraphQL\Customer\Types;

use App\GraphQL\Scalars\BigInteger;
use App\Models\Company\Phone;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class PhoneGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'PhoneGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Phone $root) => $root->id,
            ],
            'phone' => [
                'type' => Type::nonNull(GraphQL::type(BigInteger::GRAPHQL_TYPE_NAME)),
                'resolve' => fn (Phone $root) => (string) $root->phone,
            ],
            'type' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Phone $root) => $root->pivot->type,
            ],
            'comment' => [
                'type' => Type::string(),
                'resolve' => fn (Phone $root) => $root->pivot->comment,
            ],
            'installment_notifications' => [
                'type' => Type::nonNull(Type::boolean()),
                'resolve' => fn (Phone $root) => $root->pivot->installment_notifications,
            ],
            'created_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Phone $root) => $root->pivot->created_at->timestamp,
            ],
            'updated_at' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Phone $root) => $root->pivot->updated_at->timestamp,
            ],
        ];
    }
}
