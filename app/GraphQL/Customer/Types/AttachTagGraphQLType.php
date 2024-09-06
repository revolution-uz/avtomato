<?php

namespace App\GraphQL\Customer\Types;

use App\Models\Company\Customer;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AttachTagGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'AttachTagGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
    ];

    public function fields(): array
    {
        return [
            'customer_id' => [
                'type' => Type::nonNull(Type::int()),
                'resolve' => fn (Customer $root) => $root->id,
            ],
            'tags' => [
                'type' => Type::nonNull(Type::listOf(GraphQL::type(TagGraphQLType::GRAPHQL_TYPE_NAME))),
                'resolve' => fn (Customer $root) => $root->tags,
            ],
        ];
    }
}
