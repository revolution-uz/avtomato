<?php

namespace App\GraphQL\Common\Types;

use Rebing\GraphQL\Support\Type as GraphQLType;

class ArrayGraphQLType extends GraphQLType
{
    public const GRAPHQL_TYPE_NAME = 'ArrayGraphQLType';

    protected $attributes = [
        'name' => self::GRAPHQL_TYPE_NAME,
        'description' => 'Array GraphQL Type',
    ];
}
